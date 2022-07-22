jQuery(function ($) {
  /**
   * ハンバーガーメニュー クリック時
   */
  $(".js-hamburger").click(function () {
    $("body").toggleClass("is-fixed");

    if ($(this).attr("aria-expanded") == "false") {
      $(this).attr("aria-expanded", true);
      $(".js-drawer").attr("aria-hidden", false);
      $(".js-drawer").attr("data-click", true);
    } else {
      $(this).attr("aria-expanded", false);
      $(".js-drawer").attr("aria-hidden", true);
      $(".js-drawer").attr("data-click", false);
    }
  });

  $(".js-gnav-link").click(function () {
    $("body").toggleClass("is-fixed");
    $(".js-hamburger").attr("aria-expanded", false);
    $(".js-drawer").attr("aria-hidden", true);
    $(".js-drawer").attr("data-click", false);
  });

  /**
   * ウィンドウ幅リサイズ
   */
  $(window).resize(function () {
    // ドロワーオープン時、幅のサイズがPCになったらドロワーを閉じる
    let w = $(window).width();
    if (w >= 768) {
      $(".js-hamburger").attr("aria-expanded", false);
      $(".js-drawer").attr("aria-hidden", true);
      $(".js-drawer").attr("data-click", false);
    }
  });

  /**
   * スムーススクロール
   */
  $(document).on("click", 'a[href*="#"]', function () {
    let time = 400;
    let header = $("header").innerHeight();
    let target = $(this.hash);
    if (!target.length) return;
    let targetY = target.offset().top - header;
    $("html,body").animate({ scrollTop: targetY }, time, "swing");
    return false;
  });
});
