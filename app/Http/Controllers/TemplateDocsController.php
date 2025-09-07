<?php

namespace App\Http\Controllers;

use App\Models\TemplateDocs;
use App\Models\TemplateDocsPlaceholder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Element\Table;

class TemplateDocsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:view template')->only(['index', 'show']);
        $this->middleware('can:create template')->only(['create', 'store']);
        $this->middleware('can:edit template')->only(['edit', 'update']);
        $this->middleware('can:delete template')->only(['destroy']);
    }

    public function index()
    {
        $templates = TemplateDocs::all();
        return view('template.index', compact('templates'));
    }

    /**
     * Extract placeholders dari file .docx dengan membaca langsung XML (lebih akurat).
     * Mengambil dari document.xml, header, dan footer.
     */
    /**
     * [VERSI BARU & FINAL] Ekstrak placeholder menggunakan TemplateProcessor.
     * Jauh lebih simpel dan andal.
     */
    private function extractPlaceholders(string $filePath): array
    {
        try {
            // 1. Buat instance TemplateProcessor dengan file template-mu
            $templateProcessor = new TemplateProcessor($filePath);

            // 2. Gunakan method getVariables() untuk langsung dapat semua placeholder!
            $placeholders = $templateProcessor->getVariables();

            // Hasilnya adalah array, jadi kita langsung kembalikan saja.
            return $placeholders;
        } catch (\Exception $e) {
            // ---- INI BAGIAN PENTING UNTUK DEBUGGING ----
            // Hentikan eksekusi dan tampilkan pesan error yang sebenarnya.
            // Ini akan menunjukkan kepada kita akar masalahnya.
            dd('TemplateProcessor Gagal: ' . $e->getMessage(), $e);

            // Baris ini tidak akan pernah dieksekusi karena dd() menghentikan program,
            // tapi kita biarkan di sini untuk kelengkapan.
            return [];
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'template' => 'required|mimes:docx',
            'nama' => 'required|string',
            'jenis' => 'required|in:sitr,rdtr,kkpr'
        ]);

        $path = $request->file('template')->store('templates', 'public');

        // Panggil fungsi extractPlaceholders yang sudah diperbaiki
        // $placeholders = $this->extractPlaceholders(storage_path('app/' . $path));

        $placeholders = $this->extractPlaceholders(Storage::disk('public')->path($path));

        // Jika tidak ada placeholder ditemukan, kamu bisa kasih feedback atau lanjutkan saja
        if (empty($placeholders)) {
            // Bisa kasih warning di sini jika mau
            // return redirect()->back()->with('warning', 'Tidak ada placeholder ditemukan di file template.');
        }

        $template = TemplateDocs::create([
            'var_nama' => $request->nama,
            'var_file_path' => $path,
            'enum_jenis' => $request->jenis
        ]);

        foreach ($placeholders as $p) {
            TemplateDocsPlaceholder::create([
                'fk_template_docs_id' => $template->id,
                'var_key' => $p
            ]);
        }

        return redirect()->route('template.index')->with('success', 'Template berhasil disimpan dengan ' . count($placeholders) . ' placeholder ditemukan.');
    }

    // --- Sisanya (generateFromTemplate, delete) tidak perlu diubah ---
    public function generateFromTemplate(Request $request, $templateId)
    {
        $template = TemplateDocs::findOrFail($templateId);
        $storedPath = storage_path('app/' . $template->var_file_path);

        if (!file_exists($storedPath)) {
            return abort(404, 'Template file not found');
        }

        try {
            // 1. Buat instance TemplateProcessor
            $templateProcessor = new TemplateProcessor($storedPath);

            // 2. Ambil semua placeholder yang valid dari DB
            $validKeys = TemplateDocsPlaceholder::where('fk_template_id', $template->id)
                ->pluck('var_key')->toArray();

            // 3. Siapkan data pengganti dari request
            $replacements = [];
            foreach ($validKeys as $key) {
                // Ambil value dari request, fallback ke string kosong jika tidak ada
                $replacements[$key] = $request->input($key, '');
            }

            // 4. Set semua value sekaligus
            $templateProcessor->setValues($replacements);

            // 5. Simpan dokumen yang sudah di-generate ke file sementara
            $generatedDir = storage_path('app/generated');
            if (!file_exists($generatedDir)) {
                mkdir($generatedDir, 0775, true);
            }
            $outFile = $generatedDir . '/' . uniqid('doc_') . '.docx';
            $templateProcessor->saveAs($outFile);

            // 6. Download file dan hapus setelah dikirim
            return response()->download($outFile)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return abort(500, 'Gagal men-generate dokumen: ' . $e->getMessage());
        }
    }

    public function delete(Request $request, $id)
    {
        $template = TemplateDocs::findOrFail($id);
        $template->delete();

        return redirect()->route('template.index')->with('success', 'Template berhasil dihapus');
    }
}
