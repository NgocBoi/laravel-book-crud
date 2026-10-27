<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class BookExportController extends Controller
{
    /**
     * Tiêu đề cột và độ rộng cột trong file Excel.
     *
     * @var array<string, int>
     */
    private const COLUMNS = [
        'ID' => 6,
        'ISBN' => 16,
        'Tên sách' => 48,
        'Tác giả' => 28,
        'Thể loại' => 24,
        'Nhà xuất bản' => 22,
        'Năm xuất bản' => 13,
        'Số trang' => 10,
        'Kích thước' => 18,
        'Giá bán (VNĐ)' => 15,
        'Tồn kho' => 10,
        'Tình trạng' => 12,
    ];

    /** Vị trí (tính từ 0) của cột giá bán, hiển thị có phân cách hàng nghìn. */
    private const PRICE_COLUMN = 9;

    /**
     * Xuất danh sách sách (theo bộ lọc đang chọn) ra file .xlsx.
     */
    public function exportExcel(Request $request): BinaryFileResponse|RedirectResponse
    {
        if (! class_exists(ZipArchive::class)) {
            return back()->with('error', 'Máy chủ chưa bật PHP extension "zip" nên chưa xuất được file Excel.');
        }

        $books = Book::with(['authors', 'categories', 'publisher'])
            ->filter($request->only(['keyword', 'category_id', 'stock']))
            ->latest('id')
            ->get();

        $rows = $books->map(fn (Book $book): array => [
            $book->id,
            (string) $book->isbn,
            $book->title,
            $book->authors->pluck('name')->join(', '),
            $book->categories->pluck('name')->join(', '),
            (string) $book->publisher?->name,
            $book->published_year,
            $book->page_count,
            (string) $book->dimensions,
            (float) $book->price,
            $book->stock_quantity,
            $this->stockLabel($book->stock_quantity),
        ])->all();

        $path = $this->writeWorkbook($rows);

        return response()
            ->download($path, 'danh-sach-sach-'.now()->format('Ymd-His').'.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])
            ->deleteFileAfterSend();
    }

    private function stockLabel(int $stock): string
    {
        return match (true) {
            $stock === 0 => 'Hết hàng',
            $stock < Book::LOW_STOCK => 'Sắp hết',
            default => 'Còn hàng',
        };
    }

    /**
     * Ghi file .xlsx tối giản (một sheet, dòng tiêu đề in đậm, cố định và có lọc) vào thư mục tạm.
     *
     * @param  list<list<string|int|float|null>>  $rows
     */
    private function writeWorkbook(array $rows): string
    {
        $path = storage_path('framework/cache/books-export-'.Str::uuid().'.xlsx');
        $zip = new ZipArchive;
        $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            .'</Types>');

        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            .'</Relationships>');

        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .'<sheets><sheet name="Sách" sheetId="1" r:id="rId1"/></sheets>'
            .'<definedNames><definedName name="_xlnm._FilterDatabase" localSheetId="0" hidden="1">\'Sách\'!$A$1:$L$'.(count($rows) + 1).'</definedName></definedNames>'
            .'</workbook>');

        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            .'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            .'</Relationships>');

        // Style 0: mặc định, 1: tiêu đề in đậm nền xanh nhạt, 2: số có phân cách hàng nghìn
        $zip->addFromString('xl/styles.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="11"/><name val="Calibri"/></font></fonts>'
            .'<fills count="3"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill>'
            .'<fill><patternFill patternType="solid"><fgColor rgb="FFDBEAFE"/><bgColor indexed="64"/></patternFill></fill></fills>'
            .'<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            .'<cellXfs count="3"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            .'<xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1"/>'
            .'<xf numFmtId="3" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"/></cellXfs>'
            .'</styleSheet>');

        $zip->addFromString('xl/worksheets/sheet1.xml', $this->sheetXml($rows));
        $zip->close();

        return $path;
    }

    /**
     * @param  list<list<string|int|float|null>>  $rows
     */
    private function sheetXml(array $rows): string
    {
        $lastRow = count($rows) + 1;
        $cols = '';
        $column = 1;

        foreach (self::COLUMNS as $width) {
            $cols .= '<col min="'.$column.'" max="'.$column.'" width="'.$width.'" customWidth="1"/>';
            $column++;
        }

        $sheetRows = $this->rowXml(1, array_keys(self::COLUMNS), 1);

        foreach ($rows as $index => $row) {
            $sheetRows .= $this->rowXml($index + 2, $row);
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<sheetViews><sheetView workbookViewId="0"><pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>'
            .'<cols>'.$cols.'</cols>'
            .'<sheetData>'.$sheetRows.'</sheetData>'
            .'<autoFilter ref="A1:L'.$lastRow.'"/>'
            .'</worksheet>';
    }

    /**
     * @param  list<string|int|float|null>  $values
     */
    private function rowXml(int $rowNumber, array $values, int $style = 0): string
    {
        $cells = '';

        foreach (array_values($values) as $index => $value) {
            $ref = chr(65 + $index).$rowNumber;

            if ($value === null || $value === '') {
                continue;
            }

            if (is_int($value) || is_float($value)) {
                $numberStyle = $index === self::PRICE_COLUMN ? 2 : $style;
                $cells .= '<c r="'.$ref.'"'.($numberStyle ? ' s="'.$numberStyle.'"' : '').'><v>'.$value.'</v></c>';

                continue;
            }

            $text = htmlspecialchars(preg_replace('/[^\x{9}\x{A}\x{D}\x{20}-\x{D7FF}\x{E000}-\x{FFFD}]/u', '', $value), ENT_XML1 | ENT_QUOTES, 'UTF-8');
            $cells .= '<c r="'.$ref.'" t="inlineStr"'.($style ? ' s="'.$style.'"' : '').'><is><t xml:space="preserve">'.$text.'</t></is></c>';
        }

        return '<row r="'.$rowNumber.'">'.$cells.'</row>';
    }
}
