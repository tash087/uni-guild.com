/**
 * 箱庭クラフト 共通スライドショー制御スクリプト
 */
document.addEventListener('DOMContentLoaded', () => {
    const slider = document.getElementById('eventSlider');
    const dotsContainer = document.getElementById('sliderDots');
    const slides = document.querySelectorAll('.event-slide');

    // ページ内にスライド要素が存在しない場合は実行しない
    if (!slider || slides.length === 0) return;

    let currentIdx = 0;

    /**
     * 指定したインデックスのスライドを表示する
     */
    window.updateSlider = function() {
        // スライド本体を移動
        slider.style.transform = `translateX(-${currentIdx * 100}%)`;

        // ドットの活性状態を更新
        const dots = document.querySelectorAll('.dot');
        dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === currentIdx);
        });
    };

    /**
     * 前後ボタン用の移動関数
     * @param {number} step - 移動する方向 (1 または -1)
     */
    window.moveSlide = function(step) {
        currentIdx = (currentIdx + step + slides.length) % slides.length;
        updateSlider();
    };

    /**
     * 直接指定したスライドへ移動する関数
     * @param {number} index - 表示したいスライドの番号
     */
    window.goToSlide = function(index) {
        currentIdx = index;
        updateSlider();
    };

    // --- 初期設定 ---

    // 1. スライドの枚数分だけドット（インジケーター）を生成
    if (dotsContainer) {
        slides.forEach((_, i) => {
            const dot = document.createElement('span');
            dot.className = 'dot' + (i === 0 ? ' active' : '');
            dot.setAttribute('title', `Slide ${i + 1}`);
            dot.onclick = () => goToSlide(i);
            dotsContainer.appendChild(dot);
        });
    }

    // 2. スマホ向け：スワイプ操作の簡易実装（オプション）
    let touchStartX = 0;
    slider.addEventListener('touchstart', (e) => {
        touchStartX = e.touches[0].clientX;
    }, {passive: true});

    slider.addEventListener('touchend', (e) => {
        let touchEndX = e.changedTouches[0].clientX;
        if (touchStartX - touchEndX > 50) moveSlide(1);  // 左スワイプ -> 次へ
        if (touchStartX - touchEndX < -50) moveSlide(-1); // 右スワイプ -> 前へ
    }, {passive: true});
});