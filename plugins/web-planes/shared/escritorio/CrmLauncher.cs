// CRM — aplicación de escritorio de Windows.
// Ventana propia (WebView2, el mismo componente de Microsoft que usan Teams u Outlook) con el logo «re».
// Al abrirse enciende MySQL (XAMPP) y el servidor del CRM si no están encendidos.
// Se compila con build.ps1 (compilador de C# que trae Windows; C# 5).
using System;
using System.Diagnostics;
using System.Drawing;
using System.IO;
using System.Net.Sockets;
using System.Runtime.InteropServices;
using System.Threading;
using System.Threading.Tasks;
using System.Windows.Forms;
using Microsoft.Web.WebView2.Core;
using Microsoft.Web.WebView2.WinForms;

internal static class CrmLauncher
{
    private const int Port = 8086;
    private const string Php = @"C:\xampp\php\php.exe";
    private const string MysqlBin = @"C:\xampp\mysql\bin";
    internal static string DataDir;
    internal static string LogFile;
    internal static string Root;

    [DllImport("user32.dll")] private static extern bool SetForegroundWindow(IntPtr hWnd);
    [DllImport("user32.dll")] private static extern bool ShowWindow(IntPtr hWnd, int nCmdShow);

    [STAThread]
    private static void Main()
    {
        // Una sola ventana: si ya está abierta, se trae al frente.
        bool isNew;
        using (var mutex = new Mutex(true, "RaulEgana.CRM.Escritorio", out isNew))
        {
            if (!isNew)
            {
                Process me = Process.GetCurrentProcess();
                foreach (Process p in Process.GetProcessesByName(me.ProcessName))
                {
                    if (p.Id != me.Id && p.MainWindowHandle != IntPtr.Zero)
                    {
                        ShowWindow(p.MainWindowHandle, 9);
                        SetForegroundWindow(p.MainWindowHandle);
                    }
                }
                return;
            }
            Root = Path.GetFullPath(Path.Combine(AppDomain.CurrentDomain.BaseDirectory, ".."));
            DataDir = Path.Combine(Environment.GetFolderPath(Environment.SpecialFolder.LocalApplicationData), "CRM-Raul");
            Directory.CreateDirectory(DataDir);
            LogFile = Path.Combine(DataDir, "lanzador.log");
            Application.EnableVisualStyles();
            Application.SetCompatibleTextRenderingDefault(false);
            Application.Run(new CrmWindow());
        }
    }

    /// <summary>Enciende la base de datos y el servidor. Devuelve null si todo va bien, o el mensaje de error.</summary>
    internal static string StartServices()
    {
        Log("Abriendo el CRM desde " + Root);
        if (!File.Exists(Path.Combine(Root, @"public\index.php")))
        {
            return "No encuentro el CRM junto a esta aplicación.\nCRM.exe debe estar en la carpeta «desktop» del CRM.";
        }
        if (!PortOpen(3306))
        {
            string mysqld = Path.Combine(MysqlBin, "mysqld.exe");
            if (!File.Exists(mysqld)) { return "No encuentro MySQL de XAMPP en " + MysqlBin + "."; }
            Log("Encendiendo MySQL");
            StartHidden(mysqld, "--defaults-file=\"" + Path.Combine(MysqlBin, "my.ini") + "\" --standalone", MysqlBin);
            if (!WaitPort(3306, 25)) { return "La base de datos no arrancó.\nAbre el panel de XAMPP, pulsa «Start» en MySQL y vuelve a abrir el CRM."; }
        }
        if (!PortOpen(Port))
        {
            if (!File.Exists(Php)) { return "No encuentro PHP en " + Php + "."; }
            Log("Encendiendo el servidor del CRM");
            StartHidden(Php, "-d display_startup_errors=0 -S 127.0.0.1:" + Port + " -t \"" + Path.Combine(Root, "public") + "\"", Root);
            if (!WaitPort(Port, 15)) { return "El CRM no arrancó en el puerto " + Port + ".\n¿Hay otro programa usando ese puerto?"; }
        }
        return null;
    }

    internal static string Url { get { return "http://127.0.0.1:" + Port + "/"; } }

    private static void StartHidden(string file, string args, string workDir)
    {
        var psi = new ProcessStartInfo(file, args);
        psi.UseShellExecute = false;
        psi.CreateNoWindow = true;
        psi.WorkingDirectory = workDir;
        psi.WindowStyle = ProcessWindowStyle.Hidden;
        Process.Start(psi);
    }

    // Comprueba IPv4 e IPv6: en Windows algunos servidores escuchan solo en una de las dos.
    private static bool PortOpen(int port)
    {
        foreach (string host in new[] { "127.0.0.1", "::1" })
        {
            try
            {
                using (var c = new TcpClient(host == "::1" ? AddressFamily.InterNetworkV6 : AddressFamily.InterNetwork))
                {
                    IAsyncResult r = c.BeginConnect(host, port, null, null);
                    if (r.AsyncWaitHandle.WaitOne(400) && c.Connected)
                    {
                        c.EndConnect(r);
                        return true;
                    }
                }
            }
            catch (Exception)
            {
            }
        }
        return false;
    }

    private static bool WaitPort(int port, int seconds)
    {
        DateTime limit = DateTime.Now.AddSeconds(seconds);
        while (DateTime.Now < limit)
        {
            if (PortOpen(port)) { return true; }
            Thread.Sleep(250);
        }
        return false;
    }

    internal static void Log(string message)
    {
        try { File.AppendAllText(LogFile, DateTime.Now.ToString("yyyy-MM-dd HH:mm:ss") + "  " + message + Environment.NewLine); }
        catch (Exception) { }
    }
}

/// <summary>Ventana principal del CRM.</summary>
internal sealed class CrmWindow : Form
{
    private readonly WebView2 _web = new WebView2();
    private readonly Label _status = new Label();

    public CrmWindow()
    {
        Text = "CRM";
        Icon = Icon.ExtractAssociatedIcon(Application.ExecutablePath);
        StartPosition = FormStartPosition.CenterScreen;
        Size = new Size(1320, 880);
        MinimumSize = new Size(420, 520);
        BackColor = Color.FromArgb(10, 13, 18);

        _status.Dock = DockStyle.Fill;
        _status.TextAlign = ContentAlignment.MiddleCenter;
        _status.ForeColor = Color.FromArgb(234, 240, 250);
        _status.Font = new Font("Segoe UI", 13f);
        _status.Text = "Abriendo tu CRM…";
        Controls.Add(_status);

        _web.Dock = DockStyle.Fill;
        _web.Visible = false;
        Controls.Add(_web);
        Shown += OnShown;
    }

    private async void OnShown(object sender, EventArgs e)
    {
        string error = await Task.Run(new Func<string>(CrmLauncher.StartServices));
        if (error != null)
        {
            CrmLauncher.Log("ERROR: " + error.Replace("\n", " "));
            MessageBox.Show(this, error + "\n\nDetalle en: " + CrmLauncher.LogFile, "CRM", MessageBoxButtons.OK, MessageBoxIcon.Error);
            Close();
            return;
        }
        try
        {
            CoreWebView2Environment env = await CoreWebView2Environment.CreateAsync(null, Path.Combine(CrmLauncher.DataDir, "ventana-webview"));
            await _web.EnsureCoreWebView2Async(env);
        }
        catch (Exception ex)
        {
            CrmLauncher.Log("ERROR WebView2: " + ex.Message);
            MessageBox.Show(this, "No se pudo abrir la ventana del CRM (WebView2).\n" + ex.Message, "CRM", MessageBoxButtons.OK, MessageBoxIcon.Error);
            Close();
            return;
        }
        CoreWebView2 core = _web.CoreWebView2;
        core.Settings.AreDevToolsEnabled = false;
        core.Settings.IsStatusBarEnabled = false;
        core.DocumentTitleChanged += delegate { Text = string.IsNullOrEmpty(core.DocumentTitle) ? "CRM" : core.DocumentTitle; };
        // Enlaces externos (WhatsApp, Google Maps, correo...) se abren fuera, en el navegador predeterminado.
        core.NewWindowRequested += delegate (object s, CoreWebView2NewWindowRequestedEventArgs args)
        {
            args.Handled = true;
            OpenExternal(args.Uri);
        };
        core.NavigationStarting += delegate (object s, CoreWebView2NavigationStartingEventArgs args)
        {
            if (!IsLocal(args.Uri))
            {
                args.Cancel = true;
                OpenExternal(args.Uri);
            }
        };
        _web.Source = new Uri(CrmLauncher.Url);
        _web.Visible = true;
        _status.Visible = false;
        CrmLauncher.Log("Ventana abierta");
    }

    private static bool IsLocal(string uri)
    {
        return uri.StartsWith(CrmLauncher.Url, StringComparison.OrdinalIgnoreCase)
            || uri.StartsWith("http://localhost:", StringComparison.OrdinalIgnoreCase)
            || uri.StartsWith("about:", StringComparison.OrdinalIgnoreCase)
            || uri.StartsWith("data:", StringComparison.OrdinalIgnoreCase);
    }

    private static void OpenExternal(string uri)
    {
        if (uri.StartsWith("http://", StringComparison.OrdinalIgnoreCase) || uri.StartsWith("https://", StringComparison.OrdinalIgnoreCase)
            || uri.StartsWith("mailto:", StringComparison.OrdinalIgnoreCase) || uri.StartsWith("tel:", StringComparison.OrdinalIgnoreCase))
        {
            try { Process.Start(new ProcessStartInfo(uri) { UseShellExecute = true }); }
            catch (Exception ex) { CrmLauncher.Log("No se pudo abrir " + uri + ": " + ex.Message); }
        }
    }
}
