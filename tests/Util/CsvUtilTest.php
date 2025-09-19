<?php

namespace App\Tests\Util;

use App\Util\RowContextUtil;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * \App\Tests\Util\CsvUtilTest.
 */
class CsvUtilTest extends KernelTestCase
{
    public function testCsvFileExists(): void
    {
        $root = self::getContainer()->getParameter('kernel.project_dir');
        $file = $root.'/tests/Resources/santander_debito.csv';
        $this->assertFileExists($file);
    }

    public function testOpenCsv(): void
    {
        $logger = self::getContainer()->get('logger');
        $root = self::getContainer()->getParameter('kernel.project_dir');
        $file = $root.'/tests/Resources/santander_debito.csv';

        $fd = fopen($file, 'r');
        $this->assertTrue(is_resource($fd));

        $logger->debug('Santander *************');
        $headers = fgetcsv($fd, null, ',', '"');
        $logger->debug(sprintf('Row: %s', var_export($headers, true)));
        $row = fgetcsv($fd, null, ',', '"');
        $logger->debug(sprintf('Row: %s', var_export($row, true)));

        $this->getValue($this->santanderConfig(), 'fecha', $headers, $row);
        $this->getValue($this->santanderConfig(), 'desc', $headers, $row);
        $this->getValue($this->santanderConfig(), 'debit', $headers, $row);
        $this->getValue($this->santanderConfig(), 'credit', $headers, $row);
        $row = fgetcsv($fd, null, ',', '"');
        $logger->debug("\n\n\n");
        $logger->debug(sprintf('Row: %s', var_export($row, true)));
        $this->getValue($this->santanderConfig(), 'fecha', $headers, $row);
        $this->getValue($this->santanderConfig(), 'desc', $headers, $row);
        $this->getValue($this->santanderConfig(), 'debit', $headers, $row);
        $this->getValue($this->santanderConfig(), 'credit', $headers, $row);

        fclose($fd);

        // From MercadoPago
        $logger->debug("\n\n\n");
        $logger->debug("\n\n\n");
        $logger->debug('Mercado pago *************');
        $file = $root.'/tests/Resources/mercado_pago.csv';
        $fd = fopen($file, 'r');
        $this->assertTrue(is_resource($fd));

        $headers = fgetcsv($fd, null, ';', '"');
        $logger->debug(sprintf('Row: %s', var_export($headers, true)));
        $row = fgetcsv($fd, null, ';', '"');
        $logger->debug(sprintf('Row: %s', var_export($row, true)));

        $this->getValue($this->mercadoPagoConfig(), 'fecha', $headers, $row);
        $this->getValue($this->mercadoPagoConfig(), 'desc', $headers, $row);
        $this->getValue($this->mercadoPagoConfig(), 'amount', $headers, $row);
    }

    public function testRowContext(): void
    {
        $logger = self::getContainer()->get('logger');
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

        $headers = fgetcsv(
            $fd,
            null,
            $delimiter,
            $enclosure
        );
        $columns = array_keys($config['columns']);
        $util = new RowContextUtil($config, $headers);

        $items = [];

        while (($row = fgetcsv($fd, null, $delimiter, $enclosure)) !== false) {
            $util->setRow($row);

            $item = [];
            foreach ($columns as $column) {
                $item[$column] = $util->getValue($column);
            }
            $items[] = $item;
        }

        fclose($fd);

        $this->assertContainsOnlyArray($items);
        $this->assertCount(118, $items);
        $last = end($items);

        $this->assertArrayHasKey('fecha', $last);
        $this->assertArrayHasKey('desc', $last);
        $this->assertArrayHasKey('fecha', $last);
        $this->assertEquals(
            'DLO*Spotify MX   MEXICO          9164555',
            $last['desc']
        );

        $logger->debug(var_export($items, true));
    }

    public function testRowContextMercadoPago(): void
    {
        $logger = self::getContainer()->get('logger');
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

        $headers = fgetcsv(
            $fd,
            null,
            $delimiter,
            $enclosure
        );
        $columns = array_keys($config['columns']);
        $util = new RowContextUtil($config, $headers);

        $items = [];
        while (($row = fgetcsv($fd, null, $delimiter, $enclosure)) !== false) {
            $util->setRow($row);

            $item = [];
            foreach ($columns as $column) {
                $item[$column] = $util->getValue($column);
            }
            $items[] = $item;
        }

        fclose($fd);

        $this->assertContainsOnlyArray($items);
        $this->assertCount(114, $items);
        $last = end($items);

        $this->assertArrayHasKey('fecha', $last);
        $this->assertArrayHasKey('desc', $last);
        $this->assertArrayHasKey('fecha', $last);
        $this->assertEquals(
            'Pago Oxxo lorenzo boturini',
            $last['desc']
        );
        $this->assertEquals('30-08-2025', $last['fecha']);
        $logger->debug(var_export($items, true));
    }

    protected function setUp(): void
    {
        $this->bootKernel();
    }

    protected function tearDown(): void
    {
        $this->ensureKernelShutdown();
    }

    private function getValue(array $config, string $cannonical, array $headers, array $row): mixed
    {
        $logger = self::getContainer()->get('logger');

        foreach ($config[$cannonical] as $value) {
            $item = array_filter(
                $headers,
                fn ($header, $index) => $header == $value,
                ARRAY_FILTER_USE_BOTH
            );

            $logger->debug(sprintf('Item found for "%s" in %s: %s', $cannonical, $value, var_export($item, true)));

            if (empty($item)) {
                continue;
            }

            $logger->debug(sprintf(
                'Value from "%s" in row: %s', $cannonical, $row[array_key_first($item)]
            ));
            break;
        }

        return null;
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
