// SCRIPT DASHBOARD ======================================================== >>

// CONSTANTES PARA ACCEDER A LOS ELEMENTOS DEL DOMM
const toogle_interactive = document.querySelector('.toggle_interactive');
const barra_navegacion = document.querySelector('.barra_navegacion');
const sub_grupo = document.querySelectorAll('.sub_grupo');
const main = document.querySelector('.main');
const perfil = document.querySelector('.main .logo_liceo');
const imgPerfil = perfil.querySelector('img');
const seccionPerfil = perfil.querySelector('.link_perfil');

// EXPANDIR Y CONTRAAER LA BARRA DE NAVEGACION ASIDE
toogle_interactive.onclick = function() {
    barra_navegacion.classList.toggle('active');
    main.classList.toggle('active');
    this.classList.toggle('open');
}

// EXPANDIR UN SUBGRUPO DE LA BARRA DE NAVEGACIÓN ASIDE
sub_grupo.forEach (items => {
    const a = items.parentElement.querySelector('a:first-child');
    a.addEventListener('click', function(e) {
        e.preventDefault();
        if (!this.classList.contains('active')) {
            sub_grupo.forEach(i => {
                const aLink = i.parentElement.querySelector('a:first-child');
                // const aLink = i.parentElement.querySelector('a');
                aLink.classList.remove('active');
                i.classList.remove('expandir');
            });
        }
        this.classList.toggle('active');
        items.classList.toggle('expandir');
    });
});

// EVENTO QUE PERMITE SABER CUANDO LA BARRA DE NAVEGACIÓN LATERAL SE CONTRAE PARA CONTRAER LOS SUBGRUPOS
let side = new ResizeObserver(() => {
    if (barra_navegacion.clientWidth < 150) {
        sub_grupo.forEach(i => {
            const aLink = i.parentElement.querySelector('a:first-child');
            aLink.classList.remove('active');
            i.classList.remove('expandir');
        });
    }
});
side.observe(barra_navegacion);

// EVENTO PARA MOSTRAR SECCIÓN DE PERFIL Y SALIR
imgPerfil.addEventListener('click', function() {
    seccionPerfil.classList.toggle('active');
});

// SCRIPT DASHBOARD ======================================================== >>
