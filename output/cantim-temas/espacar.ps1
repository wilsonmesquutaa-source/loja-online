Add-Type -AssemblyName System.Drawing
$code = @'
using System;
using System.Drawing;
using System.Drawing.Imaging;
using System.Drawing.Drawing2D;
using System.IO;
public class Espacar {
static int[] limits={0,132,264,395,525,654,789,928,1060,1197,1348,1536};
static int Easter(int x){
int[] xs={292,296,300,307,315,325,335,364,380,390,398,403,406};
int[] ys={789,780,772,770,774,782,789,789,778,774,776,781,789};
if(x<xs[0]||x>xs[xs.Length-1])return 789;
for(int i=1;i<xs.Length;i++)if(x<=xs[i])return (int)Math.Round(ys[i-1]+(ys[i]-ys[i-1])*(x-xs[i-1])/(double)(xs[i]-xs[i-1]));
return 789;
}
static int Row(int x,int y){for(int i=1;i<limits.Length;i++){int b=i==6?Easter(x):limits[i];if(y<b)return i-1;}return 10;}
public static void Run(string srcPath,string dir){
Directory.CreateDirectory(dir);
using(var src=new Bitmap(srcPath))using(var dst=new Bitmap(1024,3256,PixelFormat.Format32bppArgb)){
using(var g=Graphics.FromImage(dst))g.Clear(Color.White);
for(int y=0;y<src.Height;y++)for(int x=0;x<src.Width;x++){int r=Row(x,y);dst.SetPixel(x,y+r*160+60,src.GetPixel(x,y));}
int mismatch=0;for(int y=0;y<src.Height;y++)for(int x=0;x<src.Width;x++){int r=Row(x,y);if(src.GetPixel(x,y).ToArgb()!=dst.GetPixel(x,y+r*160+60).ToArgb())mismatch++;}
dst.Save(Path.Combine(dir,"cartela-espacada-original.png"),ImageFormat.Png);
using(var large=new Bitmap(2048,6512))using(var g=Graphics.FromImage(large)){g.InterpolationMode=InterpolationMode.HighQualityBicubic;g.PixelOffsetMode=PixelOffsetMode.HighQuality;g.DrawImage(dst,new Rectangle(0,0,2048,6512),0,0,1024,3256,GraphicsUnit.Pixel);large.Save(Path.Combine(dir,"cartela-espacada-2K.png"),ImageFormat.Png);}
for(int r=0;r<11;r++){int start=limits[r]-(r==6?20:0);int end=limits[r+1];using(var single=new Bitmap(1024,end-start+80)){using(var g=Graphics.FromImage(single))g.Clear(Color.White);for(int y=Math.Max(0,start);y<end;y++)for(int x=0;x<1024;x++)if(Row(x,y)==r)single.SetPixel(x,y-start+40,src.GetPixel(x,y));single.Save(Path.Combine(dir,(r+1).ToString("00")+"-tema.png"),ImageFormat.Png);}}
using(var preview=new Bitmap(512,1628))using(var g=Graphics.FromImage(preview)){g.InterpolationMode=InterpolationMode.HighQualityBicubic;g.DrawImage(dst,0,0,512,1628);preview.Save(Path.Combine(dir,"previa.jpg"),ImageFormat.Jpeg);}
Console.WriteLine("Pixels originais alterados: "+mismatch+". Espaco acrescentado: 160 px entre linhas; 320 px na versao 2K.");
}}
}
'@
Add-Type -TypeDefinition $code -ReferencedAssemblies System.Drawing
[Espacar]::Run('C:/Users/Will/Downloads/Imagem do Codex 19 de set. de 2026, 02_46_37.png', "$PSScriptRoot/cartela-espacada")
