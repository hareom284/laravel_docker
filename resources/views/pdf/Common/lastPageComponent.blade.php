<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Additional Page</title>
    <!-- Optional: CSS for specific styles including margins -->
    <style>
        * {
            font-family: sans-serif;
            text-rendering: optimizeLegibility;
        }

        .terms-text p {
            margin-bottom: 0 !important;
            padding-bottom: 0 !important;
            margin-top: 0 !important;
        }

        .logo-section {
            margin: 0 auto;
            text-align: center;
        }
    </style>
</head>

<body>
    @if ($current_folder_name == 'Ideology')
    <div class="logo-section">
        <img src="{{ 'data:image/png;base64,' . $company_logo }}" style="width: auto;height:150px">
    </div>
    <br/>
    @endif
    @if ($current_folder_name == 'Henglai')
        @include('pdf.Common.Henglai.paymentTerms', [
            'terms' => $terms
        ])
    @else
    <table class="tearms hide_header_and_footer" style="width:100%;font-size:12px;">
        <tbody>
            <tr>
                <td colspan="4" align="left" class="terms-text">
                    {!! $terms !!}
                </td>
                <td></td>
            </tr>
        </tbody>
    </table>
    @endif
    <br/>
    <br/>
    @if($current_folder_name == 'Henglai')
    <div style="font-size: 12px;clear:both;">
        @include('pdf.Common.signatureComponent')
    </div>
    @endif
</body>

</html>
