(() => {
'use strict';
document.querySelectorAll('[data-add-row]').forEach(button => button.addEventListener('click', () => {
 const box=document.querySelector('[data-rows="'+button.dataset.addRow+'"]'); const row=box.querySelector('[data-row]').cloneNode(true);
 const n=Number(box.dataset.next||box.querySelectorAll('[data-row]').length);box.dataset.next=String(n+1);
 row.querySelectorAll('[name]').forEach(input=>{input.name=input.name.replace(/itens\[\d+\]/,'itens['+n+']');if(input.tagName==='INPUT') input.value=input.name.includes('percentual')?'0':(input.name.includes('fator')?'1':'');});box.append(row);
}));
document.addEventListener('click', event => {
 const remove=event.target.closest('[data-remove-row]');if(remove){const row=remove.closest('[data-row]');if(row.parentElement.querySelectorAll('[data-row]').length>1)row.remove();}
 const confirm=event.target.closest('[data-confirm]');if(confirm&&!window.confirm(confirm.dataset.confirm))event.preventDefault();
 const exportButton=event.target.closest('[data-export-table]');if(exportButton){
 const table=document.getElementById(exportButton.dataset.exportTable);const csv=[...table.rows].map(row=>[...row.cells].map(cell=>{let t=cell.textContent.trim();if(/^[=+@-]/.test(t))t="'"+t;return '"'+t.replaceAll('"','""')+'"';}).join(';')).join('\r\n');
 const url=URL.createObjectURL(new Blob(['\ufeff'+csv],{type:'text/csv;charset=utf-8'}));const a=document.createElement('a');a.href=url;a.download='relatorio-pedidos.csv';a.click();URL.revokeObjectURL(url);
 }
});
document.querySelectorAll('[data-schedule-type]').forEach(select=>{const update=()=>document.querySelectorAll('[data-schedule]').forEach(el=>el.hidden=el.dataset.schedule!==select.value);select.addEventListener('change',update);update();});
document.querySelectorAll('[data-theme-preview]').forEach(img=>{
 const form=img.closest('form'),input=form.querySelector('[data-theme-upload]');let url;
 const typeSelect=form.querySelector('[data-theme-asset-type]'),preview=img.closest('[data-preview-type]');
 const library=form.querySelector('[name="biblioteca_id"]');
 const video=form.querySelector('[data-theme-video-preview]');
 let editing=null;
 const title=form.querySelector('[data-theme-editor-title]');
 const save=form.querySelector('[data-theme-save]');
 const cancel=form.querySelector('[data-theme-edit-cancel]');
 const notice=form.querySelector('[data-theme-edit-notice]');
 const editId=form.querySelector('[data-theme-edit-id]');
 const sourceFields=[typeSelect,library,input,form.querySelector('[name="nome_arquivo"]')].filter(Boolean);
 const setMode=()=>{
  form.elements.acao.value=editing?'editar_asset':'asset';
  editId.value=editing?editing.id:'';
  sourceFields.forEach(field=>field.disabled=Boolean(editing));
  title.textContent=editing?'Editar: '+editing.rotulo:'Adicionar imagem ao tema';
  save.textContent=editing?'Salvar ajustes':'Adicionar imagem ao tema';
  cancel.hidden=notice.hidden=!editing;
 };

 const update=()=>{
  const scale=Number(form.querySelector('[data-theme-scale]').value),x=Number(form.querySelector('[data-theme-x]').value),y=Number(form.querySelector('[data-theme-y]').value);
  const isLogo=['logo_desktop','logo_mobile'].includes(typeSelect?.value);
  // Logos usam exatamente object-position, escala e origem aplicados no header.
  img.style.width=(isLogo?100:scale*100)+'%';
  img.style.height=(isLogo?100:scale*100)+'%';
  img.style.left=(isLogo?0:-(scale-1)*x)+'%';
  img.style.top=(isLogo?0:-(scale-1)*y)+'%';
  img.style.objectPosition=isLogo?x+'% '+y+'%':'';
  img.style.transform=isLogo?'scale('+scale+')':'';
  img.style.transformOrigin=isLogo?x+'% '+y+'%':'';
  img.style.opacity=form.querySelector('[data-theme-opacity]').value;
  if(video)video.style.cssText=img.style.cssText;
  form.querySelector('[data-scale-output]').textContent=Math.round(scale*100)+'%';
 };
 const updatePreviewRatio=()=>{
  if(!preview)return;
  preview.style.aspectRatio=typeSelect?.value==='header_overlay'&&img.naturalWidth&&img.naturalHeight?img.naturalWidth+' / '+img.naturalHeight:'';
 };
 const updatePreviewType=()=>{
  if(preview&&typeSelect)preview.dataset.previewType=typeSelect.value;
  const mascotHelp=form.querySelector('[data-theme-mascot-help]');
  if(mascotHelp)mascotHelp.hidden=typeSelect?.value!=='mascote';
  const uploadLabel=form.querySelector('[data-theme-upload-label]');
  if(uploadLabel)uploadLabel.textContent=typeSelect.value==='mascote'?'Arquivo do mascote (WebM até 30 MB; imagem até 5 MB)':'Nova imagem (até 5 MB)';
  if(input)input.accept='image/png,image/jpeg,image/webp,image/avif'+(typeSelect.value==='mascote'?',video/webm,.webm':'');
  updatePreviewRatio();update();
 };
 const updateImage=()=>{
  if(url){URL.revokeObjectURL(url);url=null;}
  const libraryUrl=library?.selectedOptions[0]?.dataset.url;
  const file=input?.files?.[0];
  const src=editing?editing.url:(libraryUrl||(file?(url=URL.createObjectURL(file)):''));
  const isVideo=Boolean(src && (editing?editing.url.endsWith('.webm'):libraryUrl?libraryUrl.endsWith('.webm'):file?.name.toLowerCase().endsWith('.webm')));
  img.hidden=!src||isVideo;
  if(src&&!isVideo)img.src=src;else img.removeAttribute('src');
  if(video){
   video.pause();video.hidden=!isVideo;video.muted=true;
   if(isVideo){video.src=src;video.play().catch(()=>{});}else{video.removeAttribute('src');video.load();}
  }
  update();
 };
 typeSelect?.addEventListener('change',updatePreviewType);updatePreviewType();
 img.addEventListener('load',updatePreviewRatio);
 library?.addEventListener('change',updateImage);
 input?.addEventListener('change',()=>{form.querySelector('[data-theme-scale]').value='1';updateImage();});
 form.addEventListener('reset',()=>{
  editing=null;setMode();
  setTimeout(()=>{typeSelect.querySelector('[data-legacy-type]')?.remove();updateImage();updatePreviewType();},0);
 });
 document.querySelectorAll('[data-theme-edit]').forEach(button=>button.addEventListener('click',()=>{
  editing=JSON.parse(button.dataset.themeEdit);
  typeSelect.querySelector('[data-legacy-type]')?.remove();
  if(![...typeSelect.options].some(option=>option.value===editing.tipo)){
   const option=new Option(editing.rotulo,editing.tipo);option.dataset.legacyType='';typeSelect.add(option);
  }
  typeSelect.value=editing.tipo;
  input.value='';library.value='';
  for(const name of ['escala','posicao_x','posicao_y','opacidade','texto_alternativo'])form.elements[name].value=editing[name]??'';
  setMode();updateImage();updatePreviewType();
  title.scrollIntoView({block:'center',behavior:'auto'});title.focus({preventScroll:true});
 }));
 form.querySelectorAll('input[type=range]').forEach(el=>el.addEventListener('input',update));
});
(() => {
 const dialog=document.querySelector('[data-library-confirm]'),bulkForm=document.querySelector('[data-library-bulk-delete]')?.form; let pendingForm=null;
 const selected=()=>[...document.querySelectorAll('[data-library-select]:checked')];
 const updateSelection=()=>{
  const items=selected(),all=[...document.querySelectorAll('[data-library-select]')],button=document.querySelector('[data-library-bulk-delete]'),count=document.querySelector('[data-library-selection-count]');
  if(count)count.textContent=items.length+' selecionada'+(items.length===1?'':'s');
  if(button)button.disabled=!items.length;
  const selectAll=document.querySelector('[data-library-select-all]');
  if(selectAll){selectAll.checked=all.length>0&&items.length===all.length;selectAll.indeterminate=items.length>0&&items.length<all.length;}
 };
 if(!dialog) return;
 document.addEventListener('click',event=>{
  const button=event.target.closest('[data-library-delete]');
  if(!button) return;
  event.preventDefault(); pendingForm=button.form;
  dialog.querySelector('[data-library-confirm-question]').textContent='Excluir esta imagem da biblioteca?';
  dialog.showModal();
 });
 document.querySelector('[data-library-select-all]')?.addEventListener('change',event=>{
  document.querySelectorAll('[data-library-select]').forEach(input=>input.checked=event.target.checked);updateSelection();
 });
 document.querySelectorAll('[data-library-select]').forEach(input=>input.addEventListener('change',updateSelection));
 bulkForm?.addEventListener('submit',event=>{
  const items=selected(); if(!items.length){event.preventDefault();return;}
  event.preventDefault();
  const holder=bulkForm.querySelector('[data-library-bulk-inputs]');
  holder.replaceChildren(...items.map(input=>{const hidden=document.createElement('input');hidden.type='hidden';hidden.name='biblioteca_ids[]';hidden.value=input.value;return hidden;}));
  pendingForm=bulkForm;
  dialog.querySelector('[data-library-confirm-question]').textContent='Excluir '+items.length+' imagem(ns) selecionada(s)?';
  dialog.showModal();
 });
 dialog.addEventListener('close',()=>{pendingForm=null;});
 dialog.querySelector('[data-library-confirm-submit]').addEventListener('click',()=>{
  const form=pendingForm; dialog.close(); if(form) form.submit();
 });
 updateSelection();
})();
const colors=['#a84c14','#7c9e79','#5d4037','#dcb744'];
document.querySelectorAll('[data-chart]').forEach(box=>{
 const data=JSON.parse(box.dataset.chart),entries=Object.entries(data.series).filter(([,v])=>v.length);
 if(!data.labels.length||!entries.length){box.textContent='Sem dados no período.';return;}
 const ns='http://www.w3.org/2000/svg',svg=document.createElementNS(ns,'svg');svg.setAttribute('viewBox','0 0 640 270');svg.setAttribute('role','img');svg.setAttribute('aria-label','Gráfico de valores do período. Os dados também estão disponíveis na tabela abaixo.');
 const all=entries.flatMap(([,v])=>v.map(Number)),min=Math.min(0,...all),max=Math.max(1,...all),y=v=>215-(v-min)/(max-min)*180;
 const axis=document.createElementNS(ns,'line');axis.setAttribute('x1','35');axis.setAttribute('x2','615');axis.setAttribute('y1',y(0));axis.setAttribute('y2',y(0));axis.setAttribute('stroke','#ccbdb2');svg.append(axis);
 const step=560/data.labels.length,bar=Math.max(1,step/(entries.length+1));
 entries.forEach(([label,values],j)=>values.forEach((v,i)=>{const rect=document.createElementNS(ns,'rect');rect.setAttribute('x',40+i*step+j*bar);rect.setAttribute('y',Math.min(y(v),y(0)));rect.setAttribute('width',bar*.85);rect.setAttribute('height',Math.max(1,Math.abs(y(v)-y(0))));rect.setAttribute('fill',colors[j%colors.length]);const title=document.createElementNS(ns,'title');title.textContent=data.labels[i]+' · '+label+': '+Number(v).toLocaleString('pt-BR',{maximumFractionDigits:2});rect.append(title);svg.append(rect);}));
 [0,data.labels.length-1].forEach((i,j)=>{const t=document.createElementNS(ns,'text');t.setAttribute('x',j?610:35);t.setAttribute('y','250');t.setAttribute('text-anchor',j?'end':'start');t.setAttribute('font-size','11');t.textContent=data.labels[i];svg.append(t);});box.append(svg);
 const legend=document.createElement('div');legend.className='mod-chart-legend';entries.forEach(([name],i)=>{const s=document.createElement('span');s.style.setProperty('--series',colors[i%colors.length]);s.textContent=name;legend.append(s);});box.append(legend);
 const details=document.createElement('details'),summary=document.createElement('summary');summary.textContent='Ver dados do gráfico';details.append(summary);const table=document.createElement('table');
 data.labels.forEach((label,i)=>{const tr=document.createElement('tr');[label,...entries.map(([name,values])=>name+': '+Number(values[i]??0).toLocaleString('pt-BR'))].forEach(v=>{const td=document.createElement('td');td.textContent=v;tr.append(td);});table.append(tr);});details.append(table);box.append(details);
});
})();
