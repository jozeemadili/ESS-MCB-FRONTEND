<style>
  .pd{--b:#0d47a1;--o:#f28c28;--ink:#1f2937;--mut:#6b7280;--line:#e7ebf3;--soft:#f6f8fc;color:var(--ink);}
  .pd .pd-mut{color:var(--mut);}
  .pd-card{background:#fff;border:1px solid var(--line);border-radius:16px;box-shadow:0 2px 10px rgba(15,35,80,.04);margin-bottom:18px;overflow:hidden;}
  .pd-head{padding:16px 22px;border-bottom:1px solid var(--line);display:flex;align-items:center;gap:12px;flex-wrap:wrap;}
  .pd-head > i{width:36px;height:36px;border-radius:10px;background:#e8effb;color:var(--b);display:flex;align-items:center;justify-content:center;flex:0 0 36px;}
  .pd-head b{font-size:15px;} .pd-head small{display:block;color:var(--mut);font-weight:400;font-size:12px;}
  .pd-search{max-width:230px;height:38px;border-radius:10px;border:1px solid var(--line);font-size:13px;}
  .pd-btn{background:var(--b);border-color:var(--b);color:#fff !important;border-radius:10px;font-weight:700;padding:8px 16px;}
  .pd-btn:hover{background:#0b3d8a;} .pd-btn:disabled{opacity:.55;}
  .pd-btn.outline{background:#fff;color:var(--b) !important;border:1px solid #c9d6ee;}
  .pd-btn.danger{background:#fff;color:#c62828 !important;border:1px solid #f3c6c6;}
  .pd-btn.publish{background:#137a43;border-color:#137a43;}
  .pd-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:18px;}
  .pd-stat{background:#fff;border:1px solid var(--line);border-radius:14px;padding:14px 16px;display:flex;align-items:center;gap:12px;}
  .pd-stat i{width:40px;height:40px;border-radius:12px;background:#e8effb;color:var(--b);display:flex;align-items:center;justify-content:center;font-size:17px;}
  .pd-stat b{display:block;font-size:20px;font-weight:800;line-height:1.1;} .pd-stat span{color:var(--mut);font-size:12px;font-weight:600;}
  .pd-stat.ok i{background:#e6f6ee;color:#137a43;} .pd-stat.wait i{background:#fff4e5;color:#c86b00;} .pd-stat.bad i{background:#fdecec;color:#c62828;}
  .pd-table thead th{background:var(--soft);color:var(--mut);font-size:11.5px;text-transform:uppercase;letter-spacing:.04em;font-weight:700;border-bottom:1px solid var(--line);padding:11px 14px;white-space:nowrap;}
  .pd-table td{padding:12px 14px;vertical-align:middle;border-bottom:1px solid var(--line);font-size:13px;}
  .pd-table tbody tr[data-q]{cursor:pointer;} .pd-table tbody tr[data-q]:hover{background:#fafcff;}
  .pd-name{font-weight:800;color:var(--b);} .pd-name:hover{text-decoration:underline;}
  .pd-open{width:28px;height:28px;border-radius:8px;background:#e8effb;color:var(--b);display:inline-flex;align-items:center;justify-content:center;}
  .pd-pill{display:inline-block;padding:3px 11px;border-radius:20px;font-size:11.5px;font-weight:700;white-space:nowrap;}
  .pd-pill.ok{background:#e6f6ee;color:#137a43;} .pd-pill.wait{background:#fff4e5;color:#c86b00;} .pd-pill.bad{background:#fdecec;color:#c62828;}
  .pd-foot{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;padding:12px 22px;font-size:12.5px;}
  .pd-foot .pagination{margin:0;}
  .pd-empty{padding:46px 20px;text-align:center;display:flex;flex-direction:column;align-items:center;gap:6px;color:var(--mut);}
  .pd-empty i{font-size:30px;color:#b8c4dc;} .pd-empty b{color:var(--ink);font-size:15px;}
  .pd-modal{border:0;border-radius:16px;overflow:hidden;}
  .pd-modal .modal-header{border-bottom:1px solid var(--line);padding:16px 22px;align-items:flex-start;}
  .pd-modal .modal-title{font-weight:800;margin:0;} .pd-modal small{font-size:12px;}
  .pd-modal .modal-body{padding:6px 22px 18px;} .pd-modal .modal-footer{border-top:1px solid var(--line);padding:12px 22px;}
  .pd-modal label{font-size:12px;font-weight:700;color:#475569;margin-bottom:5px;}
  .pd-modal .form-control,.pd-modal .form-select{border:1px solid var(--line);border-radius:10px;font-size:13.5px;min-height:40px;}
  .pd-sec{font-size:11.5px;text-transform:uppercase;letter-spacing:.06em;font-weight:800;color:var(--b);margin:18px 0 8px;}
  .pd-seg{display:flex;border:1px solid var(--line);border-radius:10px;overflow:hidden;height:40px;}
  .pd-seg input{display:none;} .pd-seg label{flex:1;margin:0;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:13px;color:var(--mut);}
  .pd-seg input:checked + label{background:var(--b);color:#fff;}
  /* product details */
  .pd-hero{background:linear-gradient(120deg,#0d47a1 0%,#1565c0 60%,#f28c28 170%);color:#fff;padding:22px;}
  .pd-hero h4{margin:0;color:#fff;font-weight:800;font-size:20px;} .pd-hero small{opacity:.85;}
  .pd-hero .pd-pill{margin-top:10px;}
  .pd-kv{display:grid;grid-template-columns:1fr 1fr;gap:1px;background:var(--line);}
  .pd-kv div{background:#fff;padding:12px 18px;} .pd-kv span{display:block;color:var(--mut);font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:.03em;}
  .pd-kv b{font-size:14px;} .pd-kv .wide{grid-column:1 / -1;}
  .pd-actions{display:flex;gap:8px;flex-wrap:wrap;padding:14px 18px;border-top:1px solid var(--line);}
  .pd-actions .btn{flex:1;}
  .pd-note{background:#fff4e5;border:1px solid #f6d7a7;color:#7c4a03;border-radius:12px;padding:10px 14px;margin:14px 18px 0;font-size:12.5px;}
  .pd-cond{display:flex;gap:14px;padding:14px 22px;border-bottom:1px solid var(--line);align-items:flex-start;}
  .pd-cond:last-child{border-bottom:0;}
  .pd-cond .n{flex:0 0 30px;height:30px;border-radius:50%;background:#e8effb;color:var(--b);font-weight:800;display:flex;align-items:center;justify-content:center;font-size:12.5px;}
  .pd-cond .t{flex:1;font-size:13.5px;white-space:pre-line;} .pd-cond .t small{display:block;color:var(--mut);font-size:11.5px;margin-top:3px;}
  @media (max-width:991px){.pd-stats{grid-template-columns:1fr 1fr;}}
  @media (max-width:575px){.pd-stats{grid-template-columns:1fr;}.pd-search{max-width:none;flex:1 1 100%;}.pd-kv{grid-template-columns:1fr;}}
</style>
