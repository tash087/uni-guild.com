document.addEventListener("DOMContentLoaded", function() {
    // ヘッダー読み込み
    fetch('../common/header.html')
        .then(res => res.text())
        .then(data => {
            document.getElementById('header-placeholder').innerHTML = data;
            initMenu(); 
        });

    // フッター読み込み
    fetch('../common/footer.html')
        .then(res => res.text())
        .then(data => {
            document.getElementById('footer-placeholder').innerHTML = data;
        });
});

function initMenu() {
    const btn = document.getElementById('menu-toggle');
    const nav = document.getElementById('main-nav');
    if(btn && nav) {
        btn.addEventListener('click', () => {
            const exp = btn.getAttribute('aria-expanded') === 'true';
            btn.setAttribute('aria-expanded', !exp);
            nav.classList.toggle('open');
        });
    }
}