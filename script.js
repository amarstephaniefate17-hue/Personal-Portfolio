const body=document.body;
const themeBtn=document.getElementById('themeBtn');
const menuBtn=document.querySelector('.menu-btn');
const navLinks=document.querySelector('.nav-links');
const savedTheme=localStorage.getItem('portfolio-theme');
if(savedTheme) body.dataset.theme=savedTheme;
function updateThemeIcon(){if(themeBtn) themeBtn.textContent=body.dataset.theme==='dark'?'☀':'☾'}
updateThemeIcon();
if(themeBtn){themeBtn.addEventListener('click',()=>{body.dataset.theme=body.dataset.theme==='dark'?'light':'dark';localStorage.setItem('portfolio-theme',body.dataset.theme);updateThemeIcon()})}
if(menuBtn&&navLinks){menuBtn.addEventListener('click',()=>navLinks.classList.toggle('open'));navLinks.querySelectorAll('a').forEach(a=>a.addEventListener('click',()=>navLinks.classList.remove('open')))}
const current=location.pathname.split('/').pop()||'index.html';document.querySelectorAll('.nav-links a').forEach(a=>{if(a.getAttribute('href')===current)a.classList.add('active')});
const typing=document.getElementById('typing');if(typing){const words=['Web Developer','IT Student','Creative Learner'];let wi=0,ci=0,del=false;setInterval(()=>{const word=words[wi];typing.textContent=del?word.slice(0,ci--):word.slice(0,ci++);if(!del&&ci>word.length+1){del=true}else if(del&&ci<0){del=false;wi=(wi+1)%words.length;ci=0}},110)}
const bars=document.querySelectorAll('.bar span');if(bars.length){const obs=new IntersectionObserver(entries=>entries.forEach(e=>{if(e.isIntersecting){e.target.style.width=e.target.dataset.width;obs.unobserve(e.target)}}),{threshold:.2});bars.forEach(b=>obs.observe(b))}
const filters=document.querySelectorAll('.filter'),projects=document.querySelectorAll('.project');filters.forEach(f=>f.addEventListener('click',()=>{filters.forEach(x=>x.classList.remove('active'));f.classList.add('active');const key=f.dataset.filter;projects.forEach(p=>{p.style.display=key==='all'||p.dataset.category.split(' ').includes(key)?'block':'none'})}));
const modal=document.querySelector('.modal'),modalTitle=document.getElementById('modalTitle');document.querySelectorAll('.project-img').forEach(img=>img.addEventListener('click',()=>{if(modal){modal.classList.add('show');if(modalTitle)modalTitle.textContent=img.dataset.title||'Project Preview'}}));document.querySelector('.modal-close')?.addEventListener('click',()=>modal?.classList.remove('show'));modal?.addEventListener('click',e=>{if(e.target===modal)modal.classList.remove('show')});
document.addEventListener('keydown',e=>{if(e.key==='Escape')modal?.classList.remove('show')});
const topBtn=document.getElementById('topBtn');window.addEventListener('scroll',()=>{if(topBtn)topBtn.style.display=scrollY>500?'grid':'none'});topBtn?.addEventListener('click',()=>scrollTo({top:0,behavior:'smooth'}));
const form=document.getElementById('contactForm');if(form){form.addEventListener('submit',e=>{e.preventDefault();let ok=true;const name=document.getElementById('name'),email=document.getElementById('email'),message=document.getElementById('message');[['name',name,name.value.trim().length>1,'Please enter your name.'],['email',email,/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value),'Please enter a valid email.'],['message',message,message.value.trim().length>10,'Message should be at least 10 characters.']].forEach(([id,el,valid,msg])=>{const err=document.getElementById(id+'Error');if(!valid){err.textContent=msg;el.setAttribute('aria-invalid','true');ok=false}else{err.textContent='';el.removeAttribute('aria-invalid')}});if(ok){form.reset();showToast('Message form validated successfully.')}})}
function showToast(text){const toast=document.getElementById('toast');if(!toast)return;toast.textContent=text;toast.classList.add('show');setTimeout(()=>toast.classList.remove('show'),2600)}
