<div class="header-section">
    <div class="top-header">
        <div class="left-section">
            <img src="{{ public_path() . '/images/artdecor.png' }}" height="150" />
        </div>
        <div class="right-section text-center" style="padding-top:30px;">
            <div>{{ $companies['name'] }}</div>
            <div style="width: 90%">{{ $companies['main_office'] }}</div>
            <div>Tel: (65){{ $companies['tel'] }} Tel1 :
                (65){{ $companies['fax'] }}</div>
            <div>Email: {{ $companies['email'] }}</div>
            <div>Website: www.artdecordesign.net</div>
        </div>
    </div>
</div>
