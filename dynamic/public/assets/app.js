(() => {
  const body=document.body, themeBtn=document.getElementById('themeBtn');
  const saved=localStorage.getItem('portfolio-theme'); if(saved) body.dataset.theme=saved;
  const update=()=>{if(themeBtn)themeBtn.textContent=body.dataset.theme==='dark'?'☀':'☾'}; update();
  themeBtn?.addEventListener('click',()=>{body.dataset.theme=body.dataset.theme==='dark'?'light':'dark';localStorage.setItem('portfolio-theme',body.dataset.theme);update()});
  const menu=document.querySelector('.menu-btn'), links=document.querySelector('.nav-links');
  menu?.addEventListener('click',()=>{const open=links.classList.toggle('open');menu.setAttribute('aria-expanded',String(open))});
  links?.querySelectorAll('a').forEach(a=>a.addEventListener('click',()=>{links.classList.remove('open');menu?.setAttribute('aria-expanded','false')}));
  const filters=document.querySelectorAll('.filter'), projects=document.querySelectorAll('.project');
  filters.forEach(f=>f.addEventListener('click',()=>{filters.forEach(x=>x.classList.remove('active'));f.classList.add('active');const key=f.dataset.filter;projects.forEach(p=>p.hidden=key!=='all'&&!p.dataset.category.split(/\s+/).includes(key))}));
  const modal=document.getElementById('projectModal'), modalTitle=document.getElementById('modalTitle'), modalDescription=document.getElementById('modalDescription');
  let opener=null;
  const closeModal=()=>{if(!modal)return;modal.classList.remove('show');modal.setAttribute('aria-hidden','true');opener?.focus()};
  document.querySelectorAll('.project-img').forEach(img=>{
    const card=img.closest('.project'), title=card?.querySelector('h3')?.textContent?.trim()||'Project preview';
    img.removeAttribute('aria-hidden');img.setAttribute('tabindex','0');img.setAttribute('role','button');img.setAttribute('aria-label','Preview project: '+title);
    const open=()=>{if(!modal)return;opener=img;modalTitle.textContent=title;modalDescription.textContent=card?.querySelector('.muted')?.textContent?.trim()||'';modal.classList.add('show');modal.setAttribute('aria-hidden','false');modal.querySelector('.modal-close')?.focus()};
    img.addEventListener('click',open);img.addEventListener('keydown',e=>{if(e.key==='Enter'||e.key===' '){e.preventDefault();open()}});
  });
  modal?.querySelector('.modal-close')?.addEventListener('click',closeModal);
  modal?.addEventListener('click',e=>{if(e.target===modal)closeModal()});
  document.addEventListener('keydown',e=>{if(e.key==='Escape'&&modal?.classList.contains('show'))closeModal()});
  const top=document.getElementById('topBtn'); const onScroll=()=>{if(top)top.style.display=window.scrollY>500?'grid':'none'};window.addEventListener('scroll',onScroll,{passive:true});onScroll();top?.addEventListener('click',()=>window.scrollTo({top:0,behavior:'smooth'}));
})();
