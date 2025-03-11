@if ($current_folder_name == 'Paddry')
    @include('pdf.Common.paddryTermsAndCondition', [
        'terms' => $quotationList->terms,
    ])
@else
    <table class="tearms hide_header_and_footer" style="width:100%;font-size:12px; padding-top: 20px;">
        <tbody>
            <tr>
                <td colspan="4" align="left"
                    class="{{ $current_folder_name == 'Twp' ? 'twp-terms-text' : 'terms-text' }}">{!! $terms !!}
                </td>
                <td></td>
            </tr>
        </tbody>
    </table>
@endif
