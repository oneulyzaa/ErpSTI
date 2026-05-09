<div class="row g-3 align-items-start">
    <div class="col-12 col-xl-8 d-flex flex-column gap-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="row g-0">
                    <div class="col-12 col-sm-6 p-4 border-end">
                        <div class="info-label mb-3">Dari</div>
                        <div class="fw-bold" style="font-size:15px;color:#1e293b;">PT. Sistem Teknologi Integrator</div>
                        <div class="text-muted mt-1" style="font-size:13px;line-height:1.8;">
                            Ruko Palazo Blok AB 46, Ciantra<br>
                            Cikarang Selatan, Bekasi 17530<br>
                            Telp: +6221-22108157<br>
                            marketing@stintegrator.com
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 p-4">
                        <div class="info-label mb-3">Kepada</div>
                        <div class="fw-bold" style="font-size:15px;color:#1e293b;">{{ $quotation->client_company }}</div>
                        <div class="text-muted mt-1" style="font-size:13px;line-height:1.8;">
                            @if($quotation->client_attention)Attn: {{ $quotation->client_attention }}<br>@endif
                            @if($quotation->client_cc)CC: {{ $quotation->client_cc }}<br>@endif
                            Kontak: {{ $quotation->client_name }}<br>
                            @if($quotation->client_email){{ $quotation->client_email }}@endif
                        </div>
                    </div>
                </div>
                @if($quotation->description_of_work)
                <div class="p-4 border-top bg-light">
                    <div class="info-label mb-1">Deskripsi Pekerjaan</div>
                    <div>{{ $quotation->description_of_work }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>