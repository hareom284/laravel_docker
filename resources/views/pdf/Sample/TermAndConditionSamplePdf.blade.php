<!-- resources/views/terms/show.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $termAndCondition['title'] }}</title>
</head>
<style>
    .payment-table,
    .payment-table th,
    .payment-table td {
        border: 1px solid black;
        border-collapse: collapse;
    }

    .no-border,
    .no-border th,
    .no-border td {
        border: 0 !important;
    }

    .clear-both {
        clear: both;
    }

    .signature {
        margin-top: 30px;
    }

    * {
        font-family: sans-serif;
        text-rendering: optimizeLegibility;
        font-size: 12px;
    }

    .page {
        /* overflow: hidden; */
        page-break-before: always;
    }

    .avoid-break {
        page-break-inside: avoid;
    }

    .tidplus-padding {
        padding-left: 50px !important; 
        padding-right: 50px !important; 
        padding-top: 20px;
    }

    .terms table {
        /* width: 100% !important; */
        height: auto !important;
        /* border: 1px solid black; */
    }
</style>

<body class="{{ $current_folder_name == 'Tidplus' ? 'tidplus-padding' : '' }}">
    @php
        function replacePlaceholders($content, $file, $paymentTerms, $totalPrices)
        {
            // Replace {contract_amount} with a dynamic value
            $content = str_replace('{contract_amount}', '$10,000', $content);

            if (strpos($content, '{contract_amount_text}') !== false) {
                $amountInWords = numberToWords($totalPrices, 'sample'); // Use the numberToWords function
                $content = str_replace('{contract_amount_text}', $amountInWords, $content);
            }
            
            // Replace {payment_term} with a dynamic table
            if (strpos($content, '{payment_term}') !== false) {
                $tableHtml = '<table class="payment-table">
                    <tr>
                        <td style="width: 150px" align="center">Payment Percentage</td>
                        <td>Payment Terms</td>
                        <td>Amount Payable</td>
                    </tr>';
                if ($paymentTerms && isset($paymentTerms['payment_terms'])) {
                    foreach ($paymentTerms['payment_terms'] as $paymentTerm) {
                        $amountPayable = calculateByPercent(
                            $totalPrices['total_inclusive'],
                            $paymentTerm['payment_percentage'],
                        );
                        $tableHtml .=
                            "
                        <tr>
                            <td align='center'>{$paymentTerm['payment_percentage']}%</td>
                            <td style='width: 50%;'>{$paymentTerm['payment_term']}</td>
                            <td class='percentage-total'>
                                <table class='no-border'>
                                    <tr>
                                        <td>$</td>
                                        <td align='right'>" .
                            number_format($amountPayable, 2, '.', ',') .
                            "</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>";
                    }
                }
                $tableHtml .=
                    "
                    <tr>
                        <td colspan='2' align='right'>Total Amount Payable</td>
                        <td class='percentage-total'>
                            <table class='no-border'>
                                <tr>
                                    <td>$</td>
                                    <td align='right'>" .
                    number_format($totalPrices['total_inclusive'], 2, '.', ',') .
                    "</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>";

                $content = str_replace('{payment_term}', $tableHtml, $content);
            }

            // Replace {image:left:100x100}
            if (preg_match('/{image:(left|right|center):(\d+x\d+)}/', $content, $matches)) {
                $position = $matches[1];
                $dimensions = $matches[2];
                $imageTag = $file
                    ? "<img src='$file' style='float: $position; width: " .
                        explode('x', $dimensions)[0] .
                        'px; height: ' .
                        explode('x', $dimensions)[1] .
                        "px;'>"
                    : '';
                $content = str_replace($matches[0], $imageTag, $content);
            }

            // Add style clear: both to <p> tags
            $content = preg_replace('/<p(.*?)>/', '<p$1 style="clear: both;margin:0;padding:0;" class="avoid-break">', $content);

            return $content;
        }

        function calculateByPercent($total, $percent)
        {
            return ($total * $percent) / 100;
        }
    @endphp
    @foreach ($termAndCondition['contents'] as $index => $content)
        <div class="{{ $index != 0 ? 'page' : '' }} terms">
            @foreach ($content['paragraphs'] as $paragraph)
                <div>
                    {!! replacePlaceholders($paragraph['content'], $paragraph['file'], $paymentTerms, $totalPrices) !!}
                </div>
                @if (!empty($paragraph['is_need_signature']))
                    <div class="signature" align="{{ $paragraph['signature_position'] ?? 'right' }}">
                        <p>Confirmed by: ______________</p>
                    </div>
                @endif
            @endforeach
        </div>
    @endforeach
</body>

</html>
