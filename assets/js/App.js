const toggle = document.getElementById('modeToggle');
const body = document.body;

if (localStorage.getItem('modeSombre') === 'oui') {
  body.classList.add('dark');
  if(toggle) toggle.checked = true;
}

if(toggle) {
  toggle.addEventListener('change', () => {
    if (toggle.checked) {
      body.classList.add('dark');
      localStorage.setItem('modeSombre', 'oui');
    } else {
      body.classList.remove('dark');
      localStorage.setItem('modeSombre', 'non');
    }
  });
}