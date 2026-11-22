const fs=require('node:fs'),vm=require('node:vm'),assert=require('node:assert/strict');
const s=fs.readFileSync('plib/views/scripts/index/index.php','utf8');
const fn=s.slice(s.indexOf('window.vtInstallUpdate=async function'),s.indexOf('function isStale'));
async function test(mode){
  const nodes={'forgery_protection_token':{content:'csrf-test'},'vt-update-install-btn':{},'vt-update-msg':{}};
  let requested=0,reloaded=false;
  const ctx={window:{},document:{getElementById:id=>nodes[id]},BASE:'/',URLSearchParams,vtEsc:x=>x,
    location:{reload:()=>{reloaded=true;}},setTimeout:cb=>cb(),
    fetch:async(url,args)=>{
      requested++;assert.equal(args.headers['X-Forgery-Protection-Token'],'csrf-test');
      assert.equal(args.body.get('version'),'1.31.2');
      if(mode==='network')throw new Error('Network unavailable');
      return {ok:mode!=='rejected',json:async()=>{
        if(mode==='invalid-json')throw new Error('Invalid response');
        return mode==='rejected'?{error:'Not authorized'}:{ok:true};
      }};
    },
    vtFetch:async url=>url.includes('update-status')?{state:mode==='failed'?'failed':'done',message:'Plesk rejected upgrade'}:{current:mode==='wrong-version'?'1.30.3':'1.31.2'}
  };
  vm.createContext(ctx);vm.runInContext(fn,ctx);
  await ctx.window.vtInstallUpdate('https://example.invalid/package.zip','1.31.2');
  assert.equal(requested,1,'Install must send the request, without an undefined token error');
  assert.equal(reloaded,mode==='success','Reload only after confirmed version');
  if(mode!=='success')assert.ok(nodes['vt-update-msg'].textContent.startsWith('Update failed:'));
}
(async()=>{for(const mode of ['success','rejected','network','invalid-json','failed','wrong-version'])await test(mode);console.log('Update button request token, success, and failure paths passed');})().catch(e=>{console.error(e);process.exitCode=1;});
