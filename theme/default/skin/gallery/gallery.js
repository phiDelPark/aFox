

(() => {
  'use strict'
  window.addEventListener('load', () => {
    document.querySelector('#galleryContentModal')
      ?.addEventListener('show.bs.modal', e => {
        const body = e.target.querySelector('.carousel-inner'),
          querys = e.relatedTarget.href.getQuery(),
          active = e.relatedTarget.querySelector('img')?.src.getQuery('file')
        exec_ajax({
          module:'gallery',
          act:'getFile',
          md_id:querys['id'],
          mf_srl:active
        })
        .then((data)=>{
          let html =''
          //console.log(data)
          const tag_img = '<div class="carousel-item%s">%s<img src="%s" class="d-block w-100" alt="%s"></div>'
          html += tag_img.sprintf(
            active==data.mf_srl?' active':'',
            '<h6>'+(data.mf_name || '')+', '+(data.mf_regdate || '')+'</h6>',
            './?file='+data.mf_srl, data.mf_name
          )
          body.innerHTML = html
        })
        .catch(error => console.log(error))
      })
  })
})()