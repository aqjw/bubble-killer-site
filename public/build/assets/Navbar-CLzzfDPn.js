import{a as o,o as _,g as v,w as e,b as t,m as j,d as s,t as d,u as m,k,P as L,r as B,Q as w,I as C,j as P,s as T,R as V,f as x}from"./app-C6A6k9UE.js";const D=a=>a.match(/(\b\S)?/g).join("").match(/(^\S|\S$)?/g).join(""),E={class:"text-lg font-semibold text-white absolute-center"},M={class:"mx-auto text-center p-4"},R={class:"text-lg mt-2"},I={class:"flex items-center gap-4 pr-2"},O={class:"flex items-center gap-4 pr-2"},Q={__name:"MenuProfile",props:{user:Object},setup(a){return(r,n)=>{const l=o("v-img"),g=o("v-btn"),i=o("v-icon"),p=o("v-list-item"),h=o("v-list"),u=o("v-card-text"),c=o("v-card"),b=o("v-menu");return _(),v(b,{rounded:"",location:"bottom end",origin:"auto","close-on-content-click":!1},{activator:e(({props:f})=>[t(g,j({icon:""},f),{default:e(()=>[t(l,{class:"h-10 w-10 bg-zinc-400 dark:bg-zinc-500 rounded-full mx-auto"},{default:e(()=>[s("span",E,d(m(D)(a.user.name)),1)]),_:1})]),_:2},1040)]),default:e(()=>[t(c,{width:"200px"},{default:e(()=>[t(u,{class:"!p-0"},{default:e(()=>[s("div",M,[a.user.avatar?(_(),v(l,{key:0,class:"h-16 w-16 bg-zinc-400 dark:bg-zinc-500 rounded-full mx-auto",src:r.$media.image(a.user.avatar)},null,8,["src"])):k("",!0),s("div",R,d(a.user.name),1)]),t(h,{density:"compact",class:"!pt-0"},{default:e(()=>[t(p,{to:r.route("profile.edit")},{default:e(()=>[s("div",I,[t(i,{size:"small",icon:"mdi-account-outline",variant:"tonal"}),n[0]||(n[0]=s("span",{class:"text-sm"},d("Profile"),-1))])]),_:1},8,["to"]),t(p,{"base-color":"red-lighten-1",to:{href:r.route("logout"),method:"post"}},{default:e(()=>[s("div",O,[t(i,{size:"small",icon:"mdi-logout",variant:"tonal"}),n[1]||(n[1]=s("span",{class:"text-sm"},d("Log out"),-1))])]),_:1},8,["to"])]),_:1})]),_:1})]),_:1})]),_:1})}}},U={class:"absolute top-0 right-0"},q={class:"d-flex align-center gap-3"},F={__name:"Navbar",setup(a){const r=L(),n=B(w.get("theme","light")),l=C(()=>n.value==="dark"),g=P(),{isLogged:i,user:p}=T(g);function h(){n.value=l.value?"light":"dark",r.global.name.value=n.value,w.set("theme",n.value),l.value?document.documentElement.classList.add("dark"):document.documentElement.classList.remove("dark")}return(u,c)=>{const b=o("v-chip"),f=o("v-col"),z=o("v-spacer"),y=o("v-btn"),S=o("v-row"),N=o("v-container"),$=o("v-app-bar");return _(),v($,{flat:"",density:"comfortable",class:"!shadow-"},{default:e(()=>[t(N,null,{default:e(()=>[t(S,null,{default:e(()=>[t(f,{cols:"2",class:"d-flex align-center relative"},{default:e(()=>[t(m(V),{href:u.route("home"),class:"text-2xl font-semibold mr-10 text-zinc-800 dark:text-zinc-200 no-underline"},{default:e(()=>[x(d(u.$appName)+" ",1),s("div",U,[t(b,{density:"comfortable",color:"purple"},{default:e(()=>c[0]||(c[0]=[x(" Beta ")])),_:1})])]),_:1},8,["href"])]),_:1}),t(f,{class:"mx-auto d-flex align-center justify-center"},{default:e(()=>[t(z),s("div",q,[t(y,{density:"comfortable",icon:l.value?"mdi-weather-night":"mdi-white-balance-sunny",variant:"plain",onClick:h},null,8,["icon"]),m(i)?(_(),v(Q,{key:0,user:m(p)},null,8,["user"])):k("",!0),m(i)?k("",!0):(_(),v(y,{key:1,to:u.route("login"),variant:"tonal",class:"text-none",color:"primary",density:"comfortable"},{default:e(()=>c[1]||(c[1]=[x(" Login ")])),_:1},8,["to"]))])]),_:1})]),_:1})]),_:1})]),_:1})}}};export{F as _};