<div class="panel" style="max-width:860px;margin:0 auto;">
  <div class="card" style="padding:20px;border:1px solid rgba(61,43,31,.14);border-radius:12px;">
    <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
    <style>
      .editor-wrap{border:1px solid rgba(61,43,31,.2);border-radius:10px;overflow:hidden;background:#fff}
      #editorToolbar{border:none;border-bottom:1px solid rgba(61,43,31,.14);background:#f7f2ea}
      #editorContainer{min-height:280px;font-size:14px;line-height:1.72;background:#fff}
      #editorContainer .ql-editor{min-height:260px}
      .editor-note{margin-top:6px;font-size:12px;color:#6b5f50}
    </style>

    <form action="{{ $formAction }}" method="POST" id="farmBlogForm">
      @csrf
      @if(isset($method) && $method !== 'POST')
        @method($method)
      @endif

      <div class="field" style="margin-bottom:12px;">
        <label for="author_name" style="font-weight:700;display:block;margin-bottom:6px;">Your Name</label>
        <input type="text" id="author_name" name="author_name" value="{{ old('author_name', $farming->author_name ?? '') }}" required maxlength="120" placeholder="e.g. Ramesh Patil">
      </div>

      <div class="field" style="margin-bottom:12px;">
        <label for="title" style="font-weight:700;display:block;margin-bottom:6px;">Title</label>
        <input type="text" id="title" name="title" value="{{ old('title', $farming->title ?? '') }}" required placeholder="e.g. Summer irrigation advisory for soybean">
      </div>

      <div class="field" style="margin-bottom:12px;">
        <label for="city_id" style="font-weight:700;display:block;margin-bottom:6px;">City</label>
        <select id="city_id" name="city_id" required>
          <option value="">Select city</option>
          @foreach($cities as $city)
            <option value="{{ $city->id }}" {{ (int) old('city_id', $farming->city_id ?? 0) === (int) $city->id ? 'selected' : '' }}>{{ city_display_name($city->name) }}</option>
          @endforeach
        </select>
      </div>

      <div class="field" style="margin-bottom:14px;">
        <label style="font-weight:700;display:block;margin-bottom:6px;">Blog Content</label>
        <div class="editor-wrap">
          <div id="editorToolbar">
            <span class="ql-formats">
              <select class="ql-header">
                <option selected></option>
                <option value="1"></option>
                <option value="2"></option>
                <option value="3"></option>
              </select>
            </span>
            <span class="ql-formats">
              <button class="ql-bold"></button>
              <button class="ql-italic"></button>
              <button class="ql-underline"></button>
              <button class="ql-strike"></button>
            </span>
            <span class="ql-formats">
              <select class="ql-color"></select>
              <select class="ql-background"></select>
            </span>
            <span class="ql-formats">
              <button class="ql-list" value="ordered"></button>
              <button class="ql-list" value="bullet"></button>
              <button class="ql-blockquote"></button>
            </span>
            <span class="ql-formats">
              <button class="ql-link"></button>
              <button class="ql-image"></button>
            </span>
            <span class="ql-formats">
              <button class="ql-clean"></button>
            </span>
          </div>
          <div id="editorContainer"></div>
        </div>
        <textarea id="content" name="content" required style="display:none">{{ old('content', $farming->content ?? '') }}</textarea>
        <div class="editor-note">Tip: Keep city-specific details like crop, season, and local mandi references for better impact.</div>
      </div>

      <div class="actions" style="display:flex;gap:10px;flex-wrap:wrap;">
        <button type="submit" class="button primary">Save</button>
        <a href="{{ route('farming.index') }}" class="button" style="text-decoration:none;">Cancel</a>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
<script>
  (function(){
    const contentInput = document.getElementById('content');
    const form = document.getElementById('farmBlogForm');
    const container = document.getElementById('editorContainer');

    if (!container || !contentInput || !form || typeof Quill === 'undefined') return;

    const quill = new Quill('#editorContainer', {
      theme: 'snow',
      placeholder: 'Write your farming blog here. You can use headings, bold text, colors, links, and images.',
      modules: {
        toolbar: '#editorToolbar'
      }
    });

    const initial = contentInput.value || '';
    if (initial.trim() !== '') {
      quill.clipboard.dangerouslyPasteHTML(initial);
    }

    const toolbar = quill.getModule('toolbar');
    toolbar.addHandler('image', function() {
      const url = prompt('Paste image URL (https://...)');
      if (!url) return;
      const range = quill.getSelection(true);
      quill.insertEmbed(range ? range.index : quill.getLength(), 'image', url, 'user');
    });

    form.addEventListener('submit', function(e){
      const html = quill.root.innerHTML.trim();
      const plain = quill.getText().trim();
      if (!plain) {
        e.preventDefault();
        alert('Please write your blog content.');
        return;
      }
      contentInput.value = html;
    });
  })();
</script>
