<style>
    .primary-blue{
        color: #2973c0 !important;
    }

    .secondary-blue{
        color: #14b3f2;
    }
</style>
<div class="top-header">
    <div class="left-section">
        <img src="{{ 'data:image/png;base64,' . $quotationData['companies']['company_logo'] }}" height="120" />
    </div>
    <div class="right-section" style="padding-top:30px;">
        <div class="secondary-blue"><span class="primary-blue">Address:</span> {{ $quotationData['companies']['main_office'] }}</div>
        <br/>
        <div class="secondary-blue"><span class="primary-blue">UEN:</span> @if (isset($companies['gst_reg_no']) && $companies['gst_reg_no'] != '')
            {{ $quotationData['companies']['gst_reg_no'] }}
        @else
            {{ $quotationData['companies']['reg_no'] }}
        @endif</div>
    </div>
</div>
<hr style="clear: both;height: 2px;background-color: #2973c0"/>
