/* 管理画面：画像フィールドのメディアライブラリ連携 */
(function ($) {
  'use strict';

  $(document).on('click', '.kanayama-image__pick', function (e) {
    e.preventDefault();
    var box = $(this).closest('.kanayama-image');
    var frame = wp.media({
      title: '画像を選択',
      button: { text: 'この画像を使う' },
      library: { type: 'image' },
      multiple: false
    });

    frame.on('select', function () {
      var img = frame.state().get('selection').first().toJSON();
      var url = (img.sizes && img.sizes.medium ? img.sizes.medium.url : img.url);
      box.find('input[type=hidden]').val(img.id);
      box.find('.kanayama-image__preview').html(
        $('<img>', { src: url, alt: '' }).css({
          maxWidth: '240px', height: 'auto', display: 'block', marginBottom: '8px'
        })
      );
    });

    frame.open();
  });

  $(document).on('click', '.kanayama-image__clear', function (e) {
    e.preventDefault();
    var box = $(this).closest('.kanayama-image');
    box.find('input[type=hidden]').val('');
    box.find('.kanayama-image__preview').empty();
  });
})(jQuery);
