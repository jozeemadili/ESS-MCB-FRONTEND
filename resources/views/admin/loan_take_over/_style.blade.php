<style>
  .tk{--b:#0d47a1;--o:#f28c28;--ink:#1f2937;--mut:#6b7280;--line:#e7ebf3;--soft:#f6f8fc;color:var(--ink);}
  .tk-kpis{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:14px;margin-bottom:18px;}
  .tk-kpi{display:block;background:#fff;border:1px solid var(--line);border-radius:14px;padding:14px 16px;position:relative;overflow:hidden;color:var(--ink);}
  .tk-kpi:before{content:"";position:absolute;left:0;top:0;bottom:0;width:4px;background:var(--a,var(--b));}
  .tk-kpi.on{box-shadow:0 0 0 2px var(--a,var(--b)) inset;}
  .tk-kpi span{font-size:11px;text-transform:uppercase;letter-spacing:.05em;color:var(--mut);font-weight:700;}
  .tk-kpi b{display:block;font-size:22px;font-weight:800;margin-top:4px;}
  .tk-card{background:#fff;border:1px solid var(--line);border-radius:14px;box-shadow:0 2px 10px rgba(15,35,80,.04);}
  .tk-bar{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;padding:14px 18px;border-bottom:1px solid var(--line);}
  .tk-bar b{font-size:15px;} .tk-count{margin-left:8px;color:var(--mut);font-size:12.5px;}
  .tk-search{display:flex;gap:6px;}
  .tk-search input{border:1px solid var(--line);border-radius:10px;padding:7px 12px;font-size:13px;min-width:240px;}
  .tk-search select{border:1px solid var(--line);border-radius:10px;padding:6px 8px;font-size:12.5px;background:#fff;}
  .tk-scroll{overflow:auto;}
  .tk-table{width:100%;border-collapse:separate;border-spacing:0;font-size:12.5px;}
  .tk-table th{background:var(--soft);color:#475569;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;padding:10px 12px;border-bottom:2px solid var(--line);white-space:nowrap;}
  .tk-table td{padding:10px 12px;border-bottom:1px solid var(--line);vertical-align:top;}
  .tk-table tr:hover td{background:#fafcff;}
  .tk-table .r{text-align:right;white-space:nowrap;font-variant-numeric:tabular-nums;font-weight:700;}
  .tk-sub{display:block;color:var(--mut);font-size:11.5px;margin-top:2px;}
  .tk-mono{font-family:SFMono-Regular,Menlo,Consolas,monospace;font-size:12px;}
  .tk-pill{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;white-space:nowrap;}
  .tk-pill.wait{background:#fff4e5;color:#b45309;} .tk-pill.ok{background:#e6f6ee;color:#137a43;} .tk-pill.bad{background:#fdecec;color:#c62828;} .tk-pill.grey{background:#f1f3f6;color:#55606e;} .tk-pill.blue{background:#e3f2fd;color:#0d47a1;}
  .tk-note{display:block;margin-top:4px;font-size:11.5px;color:#b45309;}
  .tk-btn{border-radius:8px;font-weight:600;font-size:12px;padding:5px 10px;white-space:nowrap;}
  .tk-empty{text-align:center;color:var(--mut);padding:44px 10px;}
  .tk-pager{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;padding:12px 18px;border-top:1px solid var(--line);font-size:12.5px;color:var(--mut);}
  .tk-pager .pagination{margin:0;}
  .tk-mt{white-space:pre-wrap;word-break:break-all;background:#0f172a;color:#e2e8f0;border-radius:10px;padding:14px;font-size:12px;max-height:55vh;overflow:auto;}
  @media (max-width:991px){.tk-kpis{grid-template-columns:repeat(2,minmax(0,1fr));}}
  @media (max-width:575px){.tk-search input{min-width:0;width:100%;} .tk-search{width:100%;flex-wrap:wrap;}}
</style>
