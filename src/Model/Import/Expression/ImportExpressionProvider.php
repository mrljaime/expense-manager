<?php

namespace App\Model\Import\Expression;

use App\Util\RowContextUtil;
use Psr\Log\LoggerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * \App\Model\Import\Expression\ImportExpressionProvider.
 */
readonly class ImportExpressionProvider implements ExpressionFunctionProviderInterface
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function getFunctions(): array
    {
        return [
            ExpressionFunction::fromPhp('abs'),
            $this->colExpression(),
            $this->moneyExpression(),
            $this->mxDate(),
        ];
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function colExpression(): ExpressionFunction
    {
        return new ExpressionFunction(
            'col',
            fn ($compiler, $name) => sprintf('\%s::evalCol($variables, %s)', self::class, $name),
            function ($variables, $name) {
                /** @var RowContextUtil $ctx */
                $ctx = $variables['_ctx'] ?? null;
                $row = $variables['row'] ?? null;

                $this->logger->debug(sprintf('Evaluating col "%s"', $name));

                if (!$ctx || !$row) {
                    throw new \InvalidArgumentException('Variables _ctx and row are required');
                }

                if (!($ctx instanceof RowContextUtil)) {
                    throw new \InvalidArgumentException('Variable _ctx must be an instance of RowContextUtil');
                }

                return $ctx
                    ->setRow($row)
                    ->getValue($name)
                ;
            }
        );
    }

    private function moneyExpression(): ExpressionFunction
    {
        return new ExpressionFunction(
            'money',
            fn ($compiler, $value) => sprintf('\%s::evalMoney(%s)', self::class, $value),
            function ($_arguments, $value) {
                $this->logger->debug(sprintf('Evaluating money "%s"', var_export($value, true)));

                if (!is_null($value) && !is_string($value)) {
                    throw new \InvalidArgumentException('Value must be a string');
                }

                if ('' === $value) {
                    return null;
                }

                // Remove parentheses and check if negative
                $negative = false;
                if (preg_match('/\(([^)]+)\)/', $value, $matches)) {
                    $value = $matches[1];
                    $negative = true;
                }

                // Standardize negative signs (handle em-dash and en-dash)
                $value = str_replace(['—', '–', '--'], '-', $value);

                // Remove currency symbols, commas, and any other non-numeric characters except minus and decimal point
                $clean = preg_replace('/[^\d.-]/', '', $value);

                // Check for explicit negative signs
                if (str_contains($clean, '-')) {
                    $negative = true;
                    $clean = str_replace('-', '', $clean);
                }

                // Convert to float and apply a negative sign if needed
                $amount = (float) $clean;

                return $negative ? -$amount : $amount;
            }
        );
    }

    /**
     * The mx_date function allows expression language to parse date in way specific format.
     *
     * In specific format, I mean that date is trying to be parsed from regex, because some dates are not
     * in standard format and also the intl doesn't work to parse some months, for example, "sept" because "sep" is
     * expected.
     *
     * So in case that happens, this function will try to get month from specified regex position.
     */
    private function mxDate(): ExpressionFunction
    {
        $map = [
            'enero' => '01', 'ene' => '01',
            'febrero' => '02', 'feb' => '02',
            'marzo' => '03', 'mar' => '03',
            'abril' => '04', 'abr' => '04',
            'mayo' => '05', 'may' => '05',
            'junio' => '06', 'jun' => '06',
            'julio' => '07', 'jul' => '07',
            'agosto' => '08', 'ago' => '08',
            'septiembre' => '09', 'sept' => '09', 'sep' => '09',
            'octubre' => '10', 'oct' => '10',
            'noviembre' => '11', 'nov' => '11',
            'diciembre' => '12', 'dic' => '12',
        ];

        return new ExpressionFunction(
            'mx_date',
            fn ($compiler, $regex, $value, $dPosition, $mPosition, $yPosition) => sprintf(
                '\%s::evalMxDate(%s, %s, %s, %s, %s)',
                self::class,
                $regex,
                $value,
                $dPosition,
                $mPosition,
                $yPosition,
            ),
            function ($_arguments, $regex, $value, $dPosition, $mPosition, $yPosition) use ($map) {
                $this->logger->debug(sprintf(
                    'Evaluating mxDate with regex "%s" -> %s',
                    var_export($regex, true),
                    var_export($value, true)
                ));

                if (is_null($value) || '' === $value) {
                    return null;
                }

                $v = mb_strtolower(trim($value), 'UTF-8');
                $v = str_replace(['-', '.'], ['/', '/'], $v);

                if (preg_match($regex, $v, $matches)) {
                    $this->logger->debug(sprintf('Matched: %s', var_export($matches, true)));
                    $day = str_pad($matches[$dPosition], 2, '0', STR_PAD_LEFT);
                    $month = $matches[$mPosition];
                    $year = $matches[$yPosition];

                    if (!ctype_digit($month)) {
                        $month = $map[$month] ?? null;
                        if (null === $month) {
                            return $value;
                        } // give up :/
                    } else {
                        $month = str_pad($month, 2, '0', STR_PAD_LEFT);
                    }

                    if (2 === strlen($year)) {
                        // simple pivot for 2000s
                        $year = (int) $year + 2000;
                    }

                    return sprintf('%04d-%s-%s', (int) $year, $month, $day);
                }

                return $value;
            }
        );
    }
}
