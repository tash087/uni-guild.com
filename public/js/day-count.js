(function() {
    function updateCounter() {
        const now = new Date();
        // 月は 0=1月 なので 8月→7, 12月→11
        const dTotal = new Date(2025, 7, 30);  // 2025/08/30
        const dHako = new Date(2025, 11, 21); // 2025/12/21

        const diffTotal = Math.floor((now - dTotal) / 86400000);
        const diffHako = Math.floor((now - dHako) / 86400000);

        const areaHako = document.getElementById('disp-hakoniwa');
        const areaTotal = document.getElementById('disp-total');

        if (areaHako && areaTotal) {
            areaHako.innerHTML = '箱庭クラフト 始動から <span class="counter-highlight">' + (diffHako > 0 ? diffHako : 0) + '</span> 日経過';
            areaTotal.innerHTML = 'サーバー開始通算[tash鯖時代～] ' + (diffTotal > 0 ? diffTotal : 0) + ' 日';
            
            // 表示できたら監視を停止
            if (window.dayCounterTimer) clearInterval(window.dayCounterTimer);
        }
    }

    // include.jsの読み込み完了を待つため、0.5秒おきにチェックを実行
    window.dayCounterTimer = setInterval(updateCounter, 500);
    
    // ページロード完了時にも念のため実行
    window.addEventListener('load', updateCounter);
})();