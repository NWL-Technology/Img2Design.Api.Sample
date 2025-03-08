using System;
using System.Drawing;
using System.Drawing.Drawing2D;
using System.Drawing.Imaging;
using System.IO;

public static class ImageHelper
{
    /// <summary>
    /// Reads the image and resizes it if it exceeds the threshold in megapixels.
    /// </summary>
    /// <param name="filePath">Path to the source image</param>
    /// <param name="maxMegaPixels">Threshold in MP (default: 10)</param>
    /// <returns>Byte array of the possibly resized image</returns>
    public static byte[] ReadBytesAndResizeIfAboveMegaPixelThreshold(string filePath, double maxMegaPixels = 4.0)
    {
        using var img = Image.FromFile(filePath);

        long width = img.Width;
        long height = img.Height;
        double mp = (width * height) / 1_000_000.0;

        if (mp <= maxMegaPixels)
        {
            return File.ReadAllBytes(filePath);
        }

        double scale = Math.Sqrt(maxMegaPixels / mp);

        int newWidth = (int)(width * scale);
        int newHeight = (int)(height * scale);

        using var resized = new Bitmap(newWidth, newHeight);
        using (var g = Graphics.FromImage(resized))
        {
            g.CompositingQuality = CompositingQuality.HighQuality;
            g.InterpolationMode = InterpolationMode.HighQualityBicubic;
            g.SmoothingMode = SmoothingMode.HighQuality;

            g.DrawImage(img, 0, 0, newWidth, newHeight);
        }

        using var ms = new MemoryStream();

        resized.Save(ms, img.RawFormat);
        return ms.ToArray();
    }
}
