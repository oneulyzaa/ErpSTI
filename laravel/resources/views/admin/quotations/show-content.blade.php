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
                        @php
                            $clientName = $quotation->client?->nama_perusahaan ?? $quotation->client_company;
                            $contactName = $quotation->client?->nama_kontak_perusahaan ?? $quotation->client_name;
                            $contactEmail = $quotation->client?->email_perusahaan ?? $quotation->client_email;
                            $contactAddress = $quotation->client_address ?? ($quotation->client?->alamat_pengiriman_perusahaan ?? '');
                        @endphp
                        <div class="fw-bold" style="font-size:15px;color:#1e293b;">{{ $clientName }}</div>
                        <div class="text-muted mt-1" style="font-size:13px;line-height:1.8;">
                            @if($quotation->client_attention)Attn: {{ $quotation->client_attention }}<br>@endif
                            @if($quotation->client_cc)CC: {{ $quotation->client_cc }}<br>@endif
                            Kontak: {{ $contactName }}<br>
                            @if($contactEmail){{ $contactEmail }}<br>@endif
                            @if($contactAddress){{ $contactAddress }}@endif
                        </div>
                    </div>
                </div>
                @if($quotation->project_name)
                <div class="p-4 border-top bg-light">
                    <div class="info-label mb-1">Nama Project</div>
                    <div class="fw-bold">{{ $quotation->project_name }}</div>
                </div>
                @endif
                @if($quotation->description_of_work)
                <div class="p-4 border-top bg-light">
                    <div class="info-label mb-1">Deskripsi Pekerjaan</div>
                    <div>{{ $quotation->description_of_work }}</div>
                </div>
                @endif
                @if($quotation->term_and_condition)
                <div class="p-4 border-top bg-light">
                    <div class="info-label mb-1">Terms & Conditions</div>
                    <div style="font-size:14px;white-space:pre-line;">{{ $quotation->term_and_condition }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>