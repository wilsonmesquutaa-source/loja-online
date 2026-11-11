Add-Type -AssemblyName System.Drawing
$code = @'
using System;
using System.Drawing;
using System.Drawing.Drawing2D;
using System.Drawing.Imaging;
using System.IO;
public class Cartela {
static Graphics g;
static Color C(string h){return ColorTranslator.FromHtml(h);}
static void Ell(string c,float x,float y,float w,float h){using(var b=new SolidBrush(C(c)))g.FillEllipse(b,x,y,w,h);}
static void Line(string c,float width,params PointF[] p){using(var pen=new Pen(C(c),width)){pen.StartCap=LineCap.Round;pen.EndCap=LineCap.Round;g.DrawLines(pen,p);}}
static PointF P(float x,float y){return new PointF(x,y);}
static void Poly(string c,params PointF[] p){using(var b=new SolidBrush(C(c)))g.FillPolygon(b,p);}
static void Heart(float x,float y,float s,string c){using(var p=new GraphicsPath()){p.AddBezier(x,y+s*.25f,x-s*.4f,y-s*.2f,x-s*.65f,y+s*.4f,x,y+s*.8f);p.AddBezier(x,y+s*.8f,x+s*.65f,y+s*.4f,x+s*.4f,y-s*.2f,x,y+s*.25f);using(var b=new SolidBrush(C(c)))g.FillPath(b,p);}}
static void Flower(float x,float y,string c){for(int i=0;i<6;i++){double a=i*Math.PI/3;Ell(c,x+(float)Math.Cos(a)*17-13,y+(float)Math.Sin(a)*17-13,26,26);}Ell("#FFC84A",x-12,y-12,24,24);}
static void Bow(float x,float y,string c){Poly(c,P(x,y),P(x-42,y-23),P(x-42,y+23));Poly(c,P(x,y),P(x+42,y-23),P(x+42,y+23));Ell("#FFD071",x-10,y-11,20,22);}
static void Hat(string c,string band){Ell(c,101,94,195,35);Poly(c,P(135,106),P(153,26),P(242,26),P(266,106));Poly(band,P(140,84),P(260,84),P(266,106),P(135,106));}
static void Star(float x,float y,float r,string c){PointF[] ps=new PointF[10];for(int i=0;i<10;i++){double a=-Math.PI/2+i*Math.PI/5;float rr=i%2==0?r:r*.43f;ps[i]=P(x+(float)Math.Cos(a)*rr,y+(float)Math.Sin(a)*rr);}Poly(c,ps);}
static void Egg(float x,float y,string c){Ell(c,x,y,43,59);Line("#FFF0C7",5,P(x+5,y+30),P(x+15,y+23),P(x+25,y+33),P(x+37,y+25));}
static void Deco(int i){
switch(i){
case 0: Poly("#C93432",P(130,104),P(177,7),P(233,14),P(275,71),P(230,51),P(243,103));Ell("#FFF7E8",125,90,133,26);Ell("#FFF7E8",260,57,31,31);Flower(382,405,"#C93432");break;
case 1: Poly("#EAB844",P(135,105),P(195,3),P(257,105));Line("#FFF2B0",7,P(156,72),P(236,72));Star(195,6,20,"#F9CF62");for(int j=0;j<8;j++){double a=j*Math.PI/4;Line("#E0A936",4,P(390+(float)Math.Cos(a)*20,66+(float)Math.Sin(a)*20),P(390+(float)Math.Cos(a)*48,66+(float)Math.Sin(a)*48));}break;
case 2: Bow(198,405,"#D73858");Heart(385,85,64,"#E44365");Heart(70,75,43,"#EF7990");break;
case 3: Hat("#E2B468","#C75035");for(int j=0;j<6;j++)Line("#C5964E",2,P(158+j*15,32),P(145+j*22,81));Line("#91602F",3,P(340,45),P(550,65),P(755,40));for(int j=0;j<7;j++){float x=348+j*56;Poly(j%2==0?"#E85E43":"#37AFA1",P(x,50),P(x+34,52),P(x+16,88));}break;
case 4: Flower(143,89,"#EF8FA6");Flower(188,72,"#F4ABC0");Flower(232,88,"#E97C9C");Heart(385,415,49,"#E97C9C");break;
case 5: Hat("#E6C78B","#36A8C0");Ell("#FFD157",365,390,68,68);Ell("#F9F1DE",379,404,40,40);Star(385,71,25,"#FFC947");break;
case 6: Ell("#FDF0D9",135,-31,42,128);Ell("#EDA5AF",146,-16,20,89);Ell("#FDF0D9",215,-31,42,128);Ell("#EDA5AF",226,-16,20,89);Egg(356,410,"#88BAA3");Egg(407,415,"#C4A1D4");break;
case 7: // Mask overlays existing glasses; transparent eye holes preserve eyes and expression.
using(var mask=new GraphicsPath(FillMode.Alternate)){mask.AddEllipse(67,214,128,94);mask.AddEllipse(233,208,126,94);mask.AddEllipse(91,235,79,49);mask.AddEllipse(257,229,77,49);using(var b=new SolidBrush(C("#7C3EB0")))g.FillPath(b,mask);using(var pen=new Pen(C("#E8BB57"),4))g.DrawPath(pen,mask);}Line("#E8BB57",9,P(191,246),P(209,239),P(235,241));for(int j=0;j<5;j++){Ell("#FFD87C",86+j*22,220,7,7);Ell("#FFD87C",250+j*21,214,7,7);}Line("#27A99A",8,P(67,250),P(31,222),P(16,252));Line("#D65B96",7,P(354,245),P(384,220),P(399,250));Star(392,84,20,"#D65B96");Star(71,93,15,"#27A99A");break;
case 8: Bow(198,409,"#397BA6");Heart(386,92,46,"#397BA6");break;
case 9: Ell("#E8BA47",141,62,116,58);Poly("#E86A50",P(198,62),P(198,115),P(145,109));Poly("#429EA7",P(198,62),P(249,105),P(198,115));Ell("#E8BA47",134,101,143,20);Line("#556B7D",4,P(198,61),P(198,39));Ell("#E86A50",164,32,69,12);Star(388,89,26,"#E8BA47");Ell("#429EA7",375,415,46,46);break;
case 10: Hat("#29282A","#CFA64D");Poly("#29282A",P(366,389),P(423,389),P(429,461),P(360,461));using(var pen=new Pen(C("#D7B369"),5))g.DrawArc(pen,379,370,29,37,180,180);Star(389,426,16,"#D7B369");break;
}}
public static void Run(string source,string dir){
using(var src=new Bitmap(source))using(var cut=new Bitmap(src.Width,src.Height,PixelFormat.Format32bppArgb)){
for(int y=0;y<src.Height;y++)for(int x=0;x<src.Width;x++){Color c=src.GetPixel(x,y);int min=Math.Min(c.R,Math.Min(c.G,c.B));int max=Math.Max(c.R,Math.Max(c.G,c.B));bool keep=(Math.Pow((x-144)/97.0,2)+Math.Pow((y-272)/64.0,2)<1)||(Math.Pow((x-290)/84.0,2)+Math.Pow((y-264)/64.0,2)<1)||(x>=221&&x<=241&&y>=250&&y<=270)||(x>=461&&x<=653&&y>=407&&y<=447);int a=255;if(!keep&&min>215)a=Math.Max(0,255-(min-215)*255/40);cut.SetPixel(x,y,Color.FromArgb(a,c.R,c.G,c.B));}
cut.Save(Path.Combine(dir,"marca-original-recortada.png"),ImageFormat.Png);
string[] names={"01-natal","02-ano-novo","03-namorados","04-sao-joao","05-maes","06-ferias","07-pascoa","08-carnaval","09-pais","10-criancas","11-black-friday"};
int width=2048,slot=820;using(var sheet=new Bitmap(width,slot*11,PixelFormat.Format32bppArgb))using(var sg=Graphics.FromImage(sheet)){
sg.Clear(Color.Transparent);
for(int i=0;i<11;i++){using(var row=new Bitmap(width,600,PixelFormat.Format32bppArgb))using(var accessories=new Bitmap(width,600,PixelFormat.Format32bppArgb)){
using(g=Graphics.FromImage(accessories)){g.Clear(Color.Transparent);g.SmoothingMode=SmoothingMode.AntiAlias;g.TranslateTransform(163,75);Deco(i);}accessories.Save(Path.Combine(dir,names[i]+"-acessorios.png"),ImageFormat.Png);
using(var rg=Graphics.FromImage(row)){rg.Clear(Color.Transparent);rg.DrawImageUnscaled(cut,163,75);rg.DrawImageUnscaled(accessories,0,0);}row.Save(Path.Combine(dir,names[i]+".png"),ImageFormat.Png);sg.DrawImageUnscaled(row,0,i*slot+40);
}}sheet.Save(Path.Combine(dir,"cantim-11-temas-2048px.png"),ImageFormat.Png);
using(var preview=new Bitmap(512,2255))using(var pg=Graphics.FromImage(preview)){pg.Clear(Color.FromArgb(240,236,228));pg.InterpolationMode=InterpolationMode.HighQualityBicubic;pg.DrawImage(sheet,0,0,512,2255);preview.Save(Path.Combine(dir,"previa.jpg"),ImageFormat.Jpeg);}
}
}}
}
'@
Add-Type -TypeDefinition $code -ReferencedAssemblies System.Drawing
[Cartela]::Run('C:/Users/Will/Pictures/Screenshots/Captura de tela 2026-09-19 024037.png', $PSScriptRoot)

