/**
 * Minified by jsDelivr using Terser v5.19.2.
 * Original file: /npm/chartjs-plugin-trendline@2.1.0/src/chartjs-plugin-trendline.js
 *
 * Do NOT use SRI with dynamically generated files! More information: https://www.jsdelivr.com/using-sri-with-dynamic-files
 */
/*!
 * chartjs-plugin-trendline.js
 * Version: 2.1.0
 *
 * Copyright 2024 Marcus Alsterfjord
 * Released under the MIT license
 * https://github.com/Makanz/chartjs-plugin-trendline/blob/master/README.md
 *
 * Mod by: vesal: accept also xy-data so works with scatter
 */
const pluginTrendlineLinear={id:"chartjs-plugin-trendline",afterDatasetsDraw:t=>{let e,i;for(let s in t.scales)if("x"==s[0]?i=t.scales[s]:e=t.scales[s],i&&e)break;const s=t.ctx;t.data.datasets.forEach(((e,a)=>{const n=e.alwaysShowTrendline||t.isDatasetVisible(a);if(e.trendlineLinear&&n&&e.data.length>1){const n=t.getDatasetMeta(a);addFitter(n,s,e,i,t.scales[n.yAxisID])}})),s.setLineDash([])}},addFitter=(t,e,i,s,a)=>{let n=i.borderColor||"rgba(169,169,169, .6)",r=i.trendlineLinear.colorMin||n,l=i.trendlineLinear.colorMax||n,o=i.trendlineLinear.width||i.borderWidth,h=i.trendlineLinear.lineStyle||"solid",d=i.trendlineLinear.fillColor;const u="object"==typeof t.controller.chart.options.parsing?t.controller.chart.options.parsing:void 0,m=i.trendlineLinear.xAxisKey||u?u.xAxisKey:"x",c=i.trendlineLinear.yAxisKey||u?u.yAxisKey:"y";o=void 0!==o?o:3;let x=new LineFitter,X=i.data.findIndex((t=>null!=t)),p=i.data.length-1,f=t.data[X][m],y=t.data[p][m],g="object"==typeof i.data[X];i.data.forEach(((t,e)=>{if(null!=t)if(["time","timeseries"].includes(s.options.type)){let i=null!=t[m]?t[m]:t.t;void 0!==i?x.add(new Date(i).getTime(),t[c]):x.add(e,t)}else g?isNaN(t.x)||isNaN(t.y)?isNaN(t.x)?isNaN(t.y)||x.add(e,t.y):x.add(e,t.x):x.add(t.x,t.y):x.add(e,t)}));let L,w,Y=s.getPixelForValue(x.minx),F=a.getPixelForValue(x.f(x.minx));if(i.trendlineLinear.projection&&x.scale()<0){let t=x.fo();t<x.minx&&(t=x.maxx),L=s.getPixelForValue(t),w=a.getPixelForValue(x.f(t))}else L=s.getPixelForValue(x.maxx),w=a.getPixelForValue(x.f(x.maxx));g||(Y=f,L=y);let T=t.controller.chart.chartArea.bottom,P=t.controller.chart.width;if(F>T){let t=F-T,e=F-w;F=T,Y+=P*(t/e)}else if(w>T){let t=w-T,e=w-F;w=T,L=P-(L-(P-P*(t/e)))}e.lineWidth=o,"dotted"===h?e.setLineDash([2,3]):e.setLineDash([]),e.beginPath(),e.moveTo(Y,F),e.lineTo(L,w);let b=e.createLinearGradient(Y,F,L,w);w<F?(b.addColorStop(0,l),b.addColorStop(1,r)):(b.addColorStop(0,r),b.addColorStop(1,l)),e.strokeStyle=b,e.stroke(),e.closePath(),d&&(e.fillStyle=d,e.beginPath(),e.moveTo(Y,F),e.lineTo(L,w),e.lineTo(L,T),e.lineTo(Y,T),e.closePath(),e.fill())};class LineFitter{constructor(){this.count=0,this.sumX=0,this.sumX2=0,this.sumXY=0,this.sumY=0,this.minx=1e100,this.maxx=-1e100,this.maxy=-1e100}add(t,e){t=parseFloat(t),e=parseFloat(e),this.count++,this.sumX+=t,this.sumX2+=t*t,this.sumXY+=t*e,this.sumY+=e,t<this.minx&&(this.minx=t),t>this.maxx&&(this.maxx=t),e>this.maxy&&(this.maxy=e)}f(t){t=parseFloat(t);let e=this.count*this.sumX2-this.sumX*this.sumX;return(this.sumX2*this.sumY-this.sumX*this.sumXY)/e+t*((this.count*this.sumXY-this.sumX*this.sumY)/e)}fo(){let t=this.count*this.sumX2-this.sumX*this.sumX;return-((this.sumX2*this.sumY-this.sumX*this.sumXY)/t)/((this.count*this.sumXY-this.sumX*this.sumY)/t)}scale(){let t=this.count*this.sumX2-this.sumX*this.sumX;return(this.count*this.sumXY-this.sumX*this.sumY)/t}}"undefined"!=typeof window&&window.Chart&&(window.Chart.hasOwnProperty("register")?window.Chart.register(pluginTrendlineLinear):window.Chart.plugins.register(pluginTrendlineLinear));try{module.exports=exports=pluginTrendlineLinear}catch(t){}
//# sourceMappingURL=/sm/5eb436d997a401774b1316b19f09b22a99f4aa622332feed384da565a38229d1.map
function calculateTrendLine(values) {
    const n = values.length;
    let sumX = 0, sumY = 0, sumXY = 0, sumXX = 0;

    // Sumatoria de valores para X (índice) e Y (valor)
    values.forEach((y, index) => {
        sumX += index;
        sumY += y;
        sumXY += index * y;
        sumXX += index * index;
    });

    // Calcula la pendiente (m) y el intercepto con el eje Y (b)
    const m = (n * sumXY - sumX * sumY) / (n * sumXX - sumX * sumX);
    const b = (sumY - m * sumX) / n;

    // Genera los puntos de la línea de tendencia
    return values.map((_, index) => m * index + b);
}