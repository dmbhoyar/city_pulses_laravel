<div class="page-config-form" style="margin-top:12px">
  <h4>Shop Page Configuration</h4>
  <p>Use the UI below to add/remove fields for your shop page. Click <strong>Save Page Config</strong> to persist.</p>

  <form action="{{ request()->path() }}" method="POST" id="page-config-form">
    @csrf
    @method('PATCH')
    <div style="margin-bottom:8px">
      <label for="shop_template">Template</label>
      <select name="template" id="shop_template">
        <option value="">Select template</option>
        <option value="simple" {{ isset($shop) && $shop->template === 'simple' ? 'selected' : '' }}>Simple</option>
        <option value="cards" {{ isset($shop) && $shop->template === 'cards' ? 'selected' : '' }}>Cards</option>
        <option value="profile" {{ isset($shop) && $shop->template === 'profile' ? 'selected' : '' }}>Profile</option>
      </select>
    </div>

    <div id="page-fields-container">
      <!-- dynamic field rows will be inserted here -->
    </div>

    <p>
      <button type="button" id="add-page-field" class="button">Add field</button>
    </p>

    <div style="margin-top:12px">
      <label for="page_config_input">Page config (JSON, auto-generated)</label><br />
      <textarea name="page_config" rows="6" style="width:100%;display:none" id="page_config_input">{{ json_encode(isset($shop) && $shop->page_config ? $shop->page_config : new stdClass()) }}</textarea>
    </div>

    <p>
      <button type="submit" class="button">Save Page Config</button>
    </p>
  </form>

  <script>
    document.addEventListener('DOMContentLoaded', function(){
      const container = document.getElementById('page-fields-container');
      const addBtn = document.getElementById('add-page-field');
      const pageConfigInput = document.getElementById('page_config_input');

      function createFieldRow(field, idx){
        field = field || { title: '', value: '', bold: false, align: 'left' };
        const row = document.createElement('div');
        row.className = 'page-field-row';
        row.style.border = '1px solid #e6e6e6';
        row.style.padding = '8px';
        row.style.marginBottom = '8px';
        row.dataset.index = idx;

        row.innerHTML = `
          <div style="display:flex;gap:8px;align-items:flex-start">
            <div style="flex:1">
              <label>Title</label><br/><input type="text" class="pf-title" value="${escapeHtml(field.title)}" style="width:100%" />
            </div>
            <div style="flex:2">
              <label>Value</label><br/><input type="text" class="pf-value" value="${escapeHtml(field.value)}" style="width:100%" />
            </div>
            <div style="width:120px">
              <label>Bold</label><br/><input type="checkbox" class="pf-bold" ${field.bold ? 'checked' : ''} />
              <br/>
              <label>Align</label><br/>
              <select class="pf-align">
                <option value="left" ${field.align=='left' ? 'selected' : ''}>Left</option>
                <option value="center" ${field.align=='center' ? 'selected' : ''}>Center</option>
                <option value="right" ${field.align=='right' ? 'selected' : ''}>Right</option>
              </select>
            </div>
            <div style="width:110px;text-align:right">
              <button type="button" class="pf-up button">↑</button>
              <button type="button" class="pf-down button">↓</button>
              <button type="button" class="pf-remove button">Remove</button>
            </div>
          </div>
        `;

        row.querySelector('.pf-remove').addEventListener('click', function(){ row.remove(); serializeToInput(); });
        row.querySelector('.pf-up').addEventListener('click', function(){ const prev = row.previousElementSibling; if(prev){ container.insertBefore(row, prev); serializeToInput(); }});
        row.querySelector('.pf-down').addEventListener('click', function(){ const next = row.nextElementSibling; if(next){ container.insertBefore(next, row); serializeToInput(); }});

        row.querySelectorAll('.pf-title, .pf-value, .pf-bold, .pf-align').forEach(function(el){ el.addEventListener('change', serializeToInput); el.addEventListener('input', serializeToInput); });

        return row;
      }

      function escapeHtml(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }

      function serializeToInput(){
        const rows = Array.from(container.querySelectorAll('.page-field-row'));
        const fields = rows.map(function(r){
          return {
            title: r.querySelector('.pf-title').value,
            value: r.querySelector('.pf-value').value,
            bold: r.querySelector('.pf-bold').checked,
            align: r.querySelector('.pf-align').value
          };
        });
        const payload = { fields: fields, template: (document.querySelector('#shop_template') && document.querySelector('#shop_template').value) || '' };
        pageConfigInput.value = JSON.stringify(payload);
      }

      addBtn.addEventListener('click', function(){ container.appendChild(createFieldRow(null, Date.now())); serializeToInput(); });

      try{
        const raw = pageConfigInput.value && pageConfigInput.value.length ? JSON.parse(pageConfigInput.value) : null;
        if(raw && Array.isArray(raw.fields)){
          raw.fields.forEach(function(f, i){ container.appendChild(createFieldRow(f, i)); });
        } else {
          container.appendChild(createFieldRow(null, 0));
        }
      }catch(e){ container.appendChild(createFieldRow(null, 0)); }

      const form = document.getElementById('page-config-form');
      form.addEventListener('submit', function(){ serializeToInput(); });
    });
  </script>
</div>
