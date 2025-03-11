<style>
    .right-header {
        float: right;
    }

    .left-header {
        float: left;
    }

    .ft-b {
        font-weight: bold;
    }

    .ft-xs {
        font-size: 11px;
    }
</style>
<div style="padding-bottom:150px;">
    <div class="left-header">
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
    </div>
    <div class="right-header">
        <div style="padding-right: 50px;">
            <img src="{{ public_path() . '/images/henglai_qr.png' }}" height="180"/>
        </div>
    </div>
</div>
