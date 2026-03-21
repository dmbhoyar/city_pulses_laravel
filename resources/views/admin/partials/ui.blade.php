<style>
  .admin-wrap{display:grid;gap:14px}
  .admin-head{display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap}
  .admin-head h1{margin:0;color:#2f4e74}
  .admin-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:10px}
  .admin-stat{background:linear-gradient(180deg,#ffffff,#f7fbff);border:1px solid #dbe7f8;border-radius:12px;padding:12px}
  .admin-stat small{display:block;font-size:11px;color:#6d84a5;text-transform:uppercase;letter-spacing:.08em}
  .admin-stat strong{display:block;font-size:24px;color:#2f4e74;margin-top:4px}
  .admin-card{background:#fff;border:1px solid #dbe7f8;border-radius:12px;padding:14px}
  .admin-card h2,.admin-card h3{margin:0 0 10px;color:#2f4e74}
  .admin-form-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:10px}
  .admin-form-grid label{display:block;font-size:12px;color:#4a6588;margin-bottom:4px;font-weight:600}
  .admin-form-grid input,.admin-form-grid select,.admin-form-grid textarea{width:100%;box-sizing:border-box;border:1px solid #d6e3f6;border-radius:8px;padding:8px 10px;background:#fff}
  .admin-form-grid textarea{min-height:72px;resize:vertical}
  .admin-actions{display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-top:10px}
  .admin-table-wrap{overflow:auto;border:1px solid #e5edf9;border-radius:10px}
  .admin-table{width:100%;border-collapse:collapse;min-width:980px;background:#fff}
  .admin-table th{background:#f3f7fd;color:#3f5e84;font-size:12px;font-weight:700;padding:9px;border-bottom:1px solid #dbe7f8;text-align:left}
  .admin-table td{padding:10px;border-bottom:1px solid #edf2fa;vertical-align:top}
  .admin-table tr:hover td{background:#fbfdff}
  .admin-tag{display:inline-flex;align-items:center;padding:3px 8px;border-radius:999px;font-size:11px;font-weight:700;background:#eef4ff;color:#32588b;border:1px solid #d4e3f8}
  .admin-toolbar{display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:10px}
  .admin-search{display:flex;gap:8px;align-items:center;flex-wrap:wrap}
  .admin-search input{min-width:230px;border:1px solid #d6e3f6;border-radius:8px;padding:8px 10px;background:#fff}
  .admin-inline{display:grid;gap:8px}
  .admin-inline .row{display:grid;grid-template-columns:repeat(3,minmax(120px,1fr));gap:8px}
  .admin-inline .row2{display:grid;grid-template-columns:repeat(2,minmax(120px,1fr));gap:8px}
  .admin-inline input,.admin-inline select,.admin-inline textarea{width:100%;box-sizing:border-box;border:1px solid #d6e3f6;border-radius:8px;padding:7px 8px;background:#fff}
  .admin-inline textarea{min-height:68px;resize:vertical}
  .admin-muted{color:#6d84a5;font-size:12px}
  @media (max-width: 900px){
    .admin-table{min-width:780px}
    .admin-inline .row,.admin-inline .row2{grid-template-columns:1fr}
  }
</style>
