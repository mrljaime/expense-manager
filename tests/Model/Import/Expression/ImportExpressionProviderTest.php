<?php

declare(strict_types=1);

namespace App\Tests\Model\Import\Expression;

use App\Model\Expression\ExpressionLanguage;
use App\Util\RowContextUtil;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * \App\Tests\Model\Import\Expression\ImportExpressionProviderTest.
 */
class ImportExpressionProviderTest extends KernelTestCase
{
    public function testDI(): void
    {
        /** @var ExpressionLanguage $expressionLanguage */
        $expressionLanguage = self::getContainer()->get(ExpressionLanguage::class);
        $this->assertNotNull($expressionLanguage);
    }

    public function testSantander(): void
    {
        /** @var LoggerInterface $logger */
        $logger = self::getContainer()->get('logger');
        /** @var ExpressionLanguage $expressionLanguage */
        $expressionLanguage = self::getContainer()->get(ExpressionLanguage::class);

        $root = self::getContainer()->getParameter('kernel.project_dir');
        $file = $root.'/tests/Resources/santander_debito.csv';

        $fd = fopen($file, 'r');
        $this->assertTrue(is_resource($fd));

        $config = [
            'dialect' => [
                'delimiter' => ',',
                'enclosure' => '"',
            ],
            'columns' => $this->santanderConfig(),
        ];

        $delimiter = $config['dialect']['delimiter'];
        $enclosure = $config['dialect']['enclosure'];

        $headers = fgetcsv($fd, null, $delimiter, $enclosure);
        $util = new RowContextUtil($config, $headers, $logger);

        $row = fgetcsv($fd, null, $delimiter, $enclosure);
        $logger->debug(sprintf('Current row: %s', var_export($row, true)));
        $val = $expressionLanguage->evaluate(
            'col("desc")',
            ['row' => $row, '_ctx' => $util]
        );

        $this->assertNotNull($val);

        $val = $expressionLanguage->evaluate(
            'col("desc") contains "VISTA"',
            ['row' => $row, '_ctx' => $util]
        );
        $this->assertTrue($val);
        $val = $expressionLanguage->evaluate(
            'col("desc") contains "PAGO"',
            ['row' => $row, '_ctx' => $util]
        );
        $this->assertFalse($val);

        $val = $expressionLanguage->evaluate(
            'money(col("debit"))',
            ['row' => $row, '_ctx' => $util]
        );
        $this->assertEquals(0, $val);

        $val = $expressionLanguage->evaluate(
            'money(col("credit"))',
            ['row' => $row, '_ctx' => $util]
        );
        $this->assertEquals(-8000, $val);
        $val = $expressionLanguage->evaluate(
            'abs(money(col("credit")))',
            ['row' => $row, '_ctx' => $util]
        );
        $this->assertEquals(8000, $val);

        $val = $expressionLanguage->evaluate(
            'money(col("debit")) || money(col("credit"))',
            ['row' => $row, '_ctx' => $util]
        );
        $this->assertEquals(-8000, $val);

        $val = $expressionLanguage->evaluate(
            'mx_date(
                "~^(\\\\d{1,2})/([a-z]+|\\\\d{1,2})/(\\\\d{2,4})$~u",
                col("fecha"),
                1, 
                2,
                3
            )',
            ['row' => $row, '_ctx' => $util]
        );
        $this->assertEquals('2025-09-15', $val);
    }

    public function testMercadoPago(): void
    {
        /** @var LoggerInterface $logger */
        $logger = self::getContainer()->get('logger');
        /** @var ExpressionLanguage $expressionLanguage */
        $expressionLanguage = self::getContainer()->get(ExpressionLanguage::class);

        $root = self::getContainer()->getParameter('kernel.project_dir');
        $file = $root.'/tests/Resources/mercado_pago.csv';

        $fd = fopen($file, 'r');
        $this->assertTrue(is_resource($fd));

        $config = [
            'dialect' => [
                'delimiter' => ';',
                'enclosure' => '"',
            ],
            'columns' => $this->mercadoPagoConfig(),
        ];

        $delimiter = $config['dialect']['delimiter'];
        $enclosure = $config['dialect']['enclosure'];

        $headers = fgetcsv($fd, null, $delimiter, $enclosure);
        $util = new RowContextUtil($config, $headers, $logger);

        $row = fgetcsv($fd, null, $delimiter, $enclosure);
        $logger->debug(sprintf('Current row: %s', var_export($row, true)));
        $val = $expressionLanguage->evaluate(
            'col("desc")',
            ['row' => $row, '_ctx' => $util]
        );

        $this->assertNotNull($val);

        $val = $expressionLanguage->evaluate(
            'col("desc") contains "Pago Total"',
            ['row' => $row, '_ctx' => $util]
        );
        $this->assertTrue($val);
        $val = $expressionLanguage->evaluate(
            'col("desc") contains "Paguito"',
            ['row' => $row, '_ctx' => $util]
        );
        $this->assertFalse($val);

        $val = $expressionLanguage->evaluate(
            'money(col("debit"))',
            ['row' => $row, '_ctx' => $util]
        );
        $this->assertEquals(0, $val);

        $val = $expressionLanguage->evaluate(
            'money(col("credit"))',
            ['row' => $row, '_ctx' => $util]
        );
        $this->assertEquals(0, $val);

        $val = $expressionLanguage->evaluate(
            'money(col("amount"))',
            ['row' => $row, '_ctx' => $util]
        );
        $this->assertEquals(-780.00, $val);

        $val = $expressionLanguage->evaluate(
            'money(col("debit")) || money(col("credit")) || money(col("amount"))',
            ['row' => $row, '_ctx' => $util]
        );
        $this->assertEquals(-780.00, $val);

        $row = fgetcsv($fd, null, $delimiter, $enclosure);
        $logger->debug(sprintf('Current row: %s', var_export($row, true)));

        $val = $expressionLanguage->evaluate(
            'col("desc")',
            ['row' => $row, '_ctx' => $util]
        );
        $this->assertEquals('Pago Cfe contigo mu', $val);
        $val = $expressionLanguage->evaluate(
            'col("desc") contains "Cfe"',
            ['row' => $row, '_ctx' => $util]
        );
        $this->assertTrue($val);
        $val = $expressionLanguage->evaluate(
            'col("desc") contains "oxxo"',
            ['row' => $row, '_ctx' => $util]
        );
        $this->assertFalse($val);
        $val = $expressionLanguage->evaluate(
            'money(col("debit")) || money(col("credit")) || money(col("amount"))',
            ['row' => $row, '_ctx' => $util]
        );
        $this->assertEquals(-646.00, $val);

        fclose($fd);
    }

    public function testMxDate(): void
    {
        /** @var LoggerInterface $logger */
        $logger = self::getContainer()->get('logger');
        $expressionLanguage = self::getContainer()->get(ExpressionLanguage::class);
        $this->assertInstanceOf(ExpressionLanguage::class, $expressionLanguage);

        $val = $expressionLanguage->evaluate(
            'mx_date(
                "~^(\\\\d{1,2})/([a-z]+|\\\\d{1,2})/(\\\\d{2,4})$~u",
                "15/sept/25",
                1,
                2,
                3
            )'
        );
        $this->assertEquals('2025-09-15', $val);
        $val = $expressionLanguage->evaluate(
            'mx_date(
                "~^(\\\\d{1,2})/([a-z]+|\\\\d{1,2})/(\\\\d{2,4})$~u",
                "30/jul/25",
                1,
                2,
                3
            )'
        );
        $this->assertEquals('2025-07-30', $val);
        $val = $expressionLanguage->evaluate(
            'mx_date(
                "~^(\\\\d{1,2})/([a-z]+|\\\\d{1,2})/(\\\\d{2,4})$~u",
                "1/jun/25",
                1,
                2,
                3
            )'
        );
        $this->assertEquals('2025-06-01', $val);
    }

    protected function setUp(): void
    {
        $this->bootKernel();
    }

    protected function tearDown(): void
    {
        $this->ensureKernelShutdown();
    }

    private function santanderConfig(): array
    {
        return [
            'fecha' => ['FECHA', 'Fecha'],
            'desc' => ['DESCRIPCION', 'CONCEPTO', 'Concepto 2'],
            'debit' => ['Depósito', 'DEPOSITO'],
            'credit' => ['Cargo', 'Crédito', 'RETIRO'],
        ];
    }

    private function mercadoPagoConfig(): array
    {
        return [
            'fecha' => ['FECHA', 'Fecha', 'RELEASE_DATE'],
            'desc' => ['DESCRIPCION', 'CONCEPTO', 'Concepto 2', 'TRANSACTION_TYPE'],
            'debit' => ['Depósito', 'DEPOSITO'],
            'credit' => ['Cargo', 'Crédito', 'RETIRO'],
            'amount' => ['TRANSACTION_NET_AMOUNT'],
        ];
    }
}
