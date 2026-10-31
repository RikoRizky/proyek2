<?php

namespace App\Support;

use App\Models\Module;
use App\Models\Requirement;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;

class AccreditationUpload
{
    public static function maxUploadKb(): int
    {
        return (int) config('accreditation.max_upload_kb', 20480);
    }

    public static function maxUploadMb(): float
    {
        return round(self::maxUploadKb() / 1024, 1);
    }

    public static function maxUploadBytes(): int
    {
        return self::maxUploadKb() * 1024;
    }

    public static function maxFileUploads(): int
    {
        $value = ini_get('max_file_uploads');

        if ($value === false || $value === '' || $value === '-1') {
            return 20;
        }

        return max(1, (int) $value);
    }

    public static function uploadLimitHint(): string
    {
        return 'Maks. '.self::maxUploadMb().' MB per berkas, '
            .self::maxFileUploads().' berkas sekaligus, '
            .'total request '.self::iniSizeLabel(ini_get('post_max_size')).'.';
    }

    /**
     * @param  array<int|string, UploadedFile|null>  $files
     */
    public static function truncatedBatchMessage(int $expectedCount, array $files): ?string
    {
        if ($expectedCount < 1) {
            return null;
        }

        $receivedCount = collect($files)
            ->filter(fn ($file) => $file instanceof UploadedFile && $file->getError() !== UPLOAD_ERR_NO_FILE)
            ->count();

        if ($receivedCount >= $expectedCount) {
            return null;
        }

        if ($receivedCount === 0 && ! request()->hasFile('files')) {
            return 'Server tidak menerima berkas unggahan. Total request mungkin melebihi batas PHP ('
                .self::iniSizeLabel(ini_get('post_max_size')).'). '
                .'Coba unggah lebih sedikit berkas sekaligus, atau jalankan ulang dengan `composer serve`.';
        }

        return 'Hanya '.$receivedCount.' dari '.$expectedCount.' berkas diterima server. '
            .'Kemungkinan batas PHP (max_file_uploads='.self::maxFileUploads()
            .', post_max_size='.self::iniSizeLabel(ini_get('post_max_size')).'). '
            .'Jalankan server dengan `composer serve` atau sesuaikan php.ini / public/.user.ini.';
    }

    public static function uploadErrorMessage(UploadedFile $file): string
    {
        return match ($file->getError()) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Berkas melebihi batas ukuran server ('
                .self::iniSizeLabel(ini_get('upload_max_filesize')).' per berkas).',
            UPLOAD_ERR_PARTIAL => 'Berkas hanya terunggah sebagian. Coba lagi.',
            UPLOAD_ERR_NO_TMP_DIR, UPLOAD_ERR_CANT_WRITE, UPLOAD_ERR_EXTENSION => 'Server gagal menyimpan berkas sementara. Hubungi administrator.',
            default => 'Berkas tidak valid atau gagal diunggah.',
        };
    }

    /** @return list<string> */
    public static function allowedMimes(): array
    {
        return config('accreditation.allowed_mimes', ['pdf', 'xlsx', 'xls']);
    }

    /** @return array<string, list<string>> */
    public static function fileRules(): array
    {
        return [
            'file' => array_merge(
                ['required', 'file', 'mimes:'.implode(',', self::allowedMimes())],
                ['max:'.self::maxUploadKb()]
            ),
        ];
    }

    public static function formatMegabytes(int $bytes): string
    {
        return number_format($bytes / 1024 / 1024, 1, ',', '.');
    }

    public static function iniSizeLabel(?string $iniValue): string
    {
        if ($iniValue === null || $iniValue === '' || $iniValue === '-1') {
            return 'tidak terbatas';
        }

        $value = trim($iniValue);
        $unit = strtolower(substr($value, -1));
        $number = (float) $value;

        return match ($unit) {
            'g' => round($number * 1024, 0).' MB',
            'm' => round($number, 0).' MB',
            'k' => round($number / 1024, 1).' MB',
            default => round($number / 1024 / 1024, 1).' MB',
        };
    }

    public static function validateFile(UploadedFile $file): ?string
    {
        $validator = Validator::make(['file' => $file], self::fileRules());

        if ($validator->passes()) {
            return null;
        }

        return $validator->errors()->first('file');
    }

    public static function fieldErrorMessage(
        Module $module,
        Requirement $requirement,
        UploadedFile $file,
        ?string $validationMessage = null,
    ): string {
        $label = '«'.$requirement->title.'» pada modul «'.$module->name.'»';
        $maxMb = self::maxUploadMb();

        if ($validationMessage !== null) {
            if (str_contains($validationMessage, 'max')) {
                $sizeMb = self::formatMegabytes($file->getSize());

                return 'Berkas '.$label.' terlalu besar ('.$sizeMb.' MB, maks. '.$maxMb.' MB). Unggah ulang berkas ini; berkas lain yang valid tetap tersimpan.';
            }

            if (str_contains($validationMessage, 'mimes')) {
                return 'Berkas '.$label.' harus PDF atau Excel. Unggah ulang berkas ini; berkas lain yang valid tetap tersimpan.';
            }
        }

        return 'Berkas '.$label.' tidak valid. Unggah ulang berkas ini; berkas lain yang valid tetap tersimpan.';
    }
}
