import SimpleMDE from 'simplemde'
require('simplemde/dist/simplemde.min.css')

const simplemde = new SimpleMDE()



// const $ = require('jquery')
// const hljs = require('highlight.js')
//
// require('../css/editor.css')
// require('highlight.js/styles/a11y-dark.css')


// const jsData = $('#js-data')
//
// const previewUrl = jsData.data('preview-url')
// const editorField = jsData.data('editor-field')
// const previewField = jsData.data('preview-field')
//
// $('a[data-toggle="tab"]').on('click', function (e) {
//     if (previewField === $(e.target).attr('href')) {
//         let out = $(previewField)
//         out.empty().addClass('loading')
//         $.post(
//             previewUrl,
//             {text: $(editorField).val()},
//             function (r) {
//                 out.html(r.data).removeClass('loading')
//                 hljs.highlightBlock(out)
                // document.querySelectorAll('pre code').forEach((block) => {
                //     hljs.highlightBlock(block);
                // });
            // }
        // )
    // }
// })

