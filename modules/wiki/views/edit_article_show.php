


<!DOCTYPE html>
<html lang="en">
  <head>
    <link rel="stylesheet" href="<?php echo base_url(WIKI_ASSETS_PATH.'/css/bootstrap.min.css'); ?>">
    <script type="text/javascript" src="<?php echo base_url(WIKI_ASSETS_PATH.'/tinymce/tinymce.min.js'); ?>"></script>
  </head>
  <body>
  <aside>
    <h2>All articles</h2>
    <section>
      <article>
        <h3>Design process</h3>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum ut nunc pellentesque, suscipit dui vel, consequat dolor.</p>
        <div>12 minutes ago</div>
      </article>

      <article>
        <h3>Skin tool ideas</h3>
        <p>Vestibulum ut nunc pellentesque, suscipit dui vel, consequat dolor.</p>
        <div>Yesterday</div>
      </article>

    </section>
  </aside>

  <header>
    <svg height="16" viewBox="0 0 15 16" width="15" xmlns="http://www.w3.org/2000/svg">
      <g fill="none" fill-rule="evenodd" transform="translate(-361 -210)">
        <rect fill="#56606f" height="16" opacity=".5" rx="1.5" width="11" x="361" y="210"></rect>
        <rect fill="#56606f" height="16" opacity=".5" rx="1" width="2" x="374" y="210"></rect>
        <path d="m373 216h-4v-3.5l-5.5 5.5 5.5 5.5v-3.5h4z" fill="#fff"></path>
      </g>
    </svg>
    <ul class="tags">
      <li>Foo</li>
      <li>Bar</li>
      <li>Travel</li>
    </ul>
    <div class="button" style="margin-left: auto;">
      <button type="button">Save</button>
     <button type="button">Delete</button>
    </div>
    
    <svg height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
      <g fill="none" fill-rule="evenodd">
        <path d="m0 0h24v24h-24z" fill="#fff"></path>
        <g fill="#aaafb7">
          <rect height="4" rx="2" width="4" x="4" y="10"></rect>
          <rect height="4" rx="2" width="4" x="10" y="10"></rect>
          <rect height="4" rx="2" width="4" x="16" y="10"></rect>
        </g>
      </g>
    </svg>
  </header>

  <main>
    <textarea id="editor">            
     <?php echo $article['content']; ?>
    </textarea>
  </main>
</body>
<style>
body {
  margin: 0;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto,
    Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
  color: #262c42;
}
main {
  position: fixed;
  top: 3rem;
  left: calc(260px + 0rem);
  right: 0rem;
  bottom: 0rem;
}

aside {
  position: fixed;
  width: 260px;
  overflow-y: scroll;
  top: 0px;
  bottom: 0;
  left: 0;
  /* padding: 1rem; */
  box-sizing: border-box;
  background-color: #ecf3f6;
  border-right: 1px solid rgba(0, 0, 0, 0.1);
}

aside h2 {
  padding: 0.75rem 1rem;
  font-size: 20px;
  font-weight: 600;
}

aside article {
  border-bottom: 1px solid rgba(0, 0, 0, 0.1);
  font-size: 13px;
  line-height: 1.3;
  padding: 0.75rem 1rem;
}

aside article:first-child {
  border-top: 1px solid rgba(0, 0, 0, 0.1);
}

aside article::after {
  content: "";
  clear: both;
  display: table;
}

aside article h3 {
  font-size: 15px;
  font-weight: 600;
  margin: 0 0 0.3rem;
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

aside article p {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  margin: 0 0 0.4rem;
  overflow: hidden;
}

aside article div {
  font-size: 11px;
  color: rgba(0, 0, 0, 0.4);
}

aside article img {
  display: block;
  width: 5rem;
  height: 5rem;
  object-fit: cover;
  float: right;
  margin-left: 0.5rem;
}

header {
  position: fixed;
  top: 0;
  left: 260px;
  right: 0;
  height: 3rem;
  display: flex;
  align-items: center;
  /* justify-content: flex-end; */
  padding: 0 2rem;
}

header > *:not(:last-child) {
  margin-right: 2rem;
}

header button {
  background: #2ba0ff;
  color: #fff;
  border-radius: 100px;
  padding: 0.5rem 1rem;
  font-size: 14px;
  font-weight: 500;
  border: none;
  outline: none;
  -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
  -webkit-user-select: none;
}

.tags {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
}

.tags li {
  display: block;
  background-color: #eeeff1;
  padding: 0.2rem 0.6rem;
  margin-right: 0.2rem;
  border-radius: 100px;
  font-size: 12px;
  font-weight: 500;
  color: #aaafb7;
}

</style>
<script type="text/javascript">

  tinymce.init({
    selector: "#editor",
    plugins:
      "codesample hr image imagetools link lists table tabfocus",
    toolbar:
      "styleselect | bold italic underline strikethrough forecolor backcolor | table image link codesample hr | bullist numlist checklist",
    menubar: false,
    statusbar: false,
    skin: "oxide",
    height: "100%",
    style_formats: [
      { title: "Title", block: "h1" },
      { title: "Heading", block: "h2" },
      { title: "Sub heading", block: "h3" },
      { title: "Paragraph", block: "p" },
      { title: "Code", inline: "code" },
      { title: "Quote", block: "blockquote" },
      { title: "Callout", block: "div", classes: "call-out" }
    ],
    content_style: "body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif; line-height: 1.4; margin: 3rem auto; max-width: 740px; } table { border-collapse: collapse; } table th, table td { border: 1px solid #ccc; padding: 0.4rem; } figure { display: table; margin: 1rem auto; } figure figcaption { color: #999; display: block; margin-top: 0.25rem; text-align: center; } hr { border-color: #ccc; border-style: solid; border-width: 1px 0 0 0; } code { background-color: #e8e8e8; border-radius: 3px; padding: 0.1rem 0.2rem; } img { max-width: 100%; } div.callout { border-radius: 4px; background-color: #f7f6f3; padding: 1rem 1rem 1rem 3rem; position: relative; } div.callout:before { content: '📣'; display: block; position: absolute; top: 1rem; left: 1rem; font-size: 20px; } .mce-content-body:not([dir=rtl]) blockquote { border-left: 2px solid #ccc; margin-left: 1.5rem; padding-left: 1rem; } .mce-content-body[dir=rtl] blockquote { border-right: 2px solid #ccc; margin-right: 1.5rem; padding-right: 1rem; }"
  });

</script>
</html>