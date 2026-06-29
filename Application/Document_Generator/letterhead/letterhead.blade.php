{{-- Shared header for all PDF reports. DomPDF only supports a subset of CSS
     (no flexbox/grid), so this uses plain tables/blocks. --}}
<table style="width:100%; margin-bottom:18px;">
    <tr>
        <td style="width:60%;">
            <div style="font-size:16px; font-weight:bold; color:#0E2A47; letter-spacing:1px;">
                KTM eDOIS
            </div>
            <div style="font-size:10px; color:#44546B;">
                Electronic Delivery Order &amp; Invoice System
            </div>
        </td>
        <td style="width:40%; text-align:right; font-size:9.5px; color:#44546B;">
            Generated: {{ \Carbon\Carbon::now()->format('d M Y, h:i A') }}<br>
            By: {{ session('staff_name', 'System') }}
        </td>
    </tr>
</table>
<div style="border-top:2px solid #0E2A47; margin-bottom:16px;"></div>
<div style="font-size:14px; font-weight:bold; color:#0E2A47; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:14px;">
    {{ $reportTitle }}
</div>
