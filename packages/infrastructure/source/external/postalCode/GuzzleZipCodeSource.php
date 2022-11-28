<?php

namespace packages\infrastructure\source\external\postalCode;

use App\Exceptions\ErrorCodeConst;
use App\Exceptions\WebAPIException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use packages\domain\model\zipcode\ZipCodeSource;
use packages\domain\model\zipcode\ZipCodeSourceRepository;
use ZipArchive;

class GuzzleZipCodeSource implements ZipCodeSourceRepository
{
    public function get(): ZipCodeSource
    {
        // 重複しない保存先を取得 /tmp
        $tempName = tempnam(sys_get_temp_dir(), 'postal_cd_');
        try {
            $response = Http::sink($tempName)
                ->post('https://www.post.japanpost.jp/zipcode/dl/kogaki/zip/ken_all.zip');
        } catch (ConnectionException) {
            throw new WebAPIException(ErrorCodeConst::ERROR_503_EXTERNAL, [], 503);
        }
        Storage::disk('local')->put('/jp/zip/ken_all.zip', $response->body());

        $zip = new ZipArchive();
        if ($zip->open(Storage::path('/jp/zip/ken_all.zip')) === true) {
            $zip->extractTo(Storage::path('/jp'));
            $zip->close();
        }
        $this->resolveEncode();
        return new ZipCodeSource(Storage::path('/jp/KEN_ALL.CSV'));
    }

    /**
     * KEN_ALL.csvのエンコード文字列変更
     * @note Shift-jis->UTF-8に変換
     * @return void
     */
    private function resolveEncode(): void
    {
        $filePath = app()->storagePath() . '/app/jp/KEN_ALL.CSV';
        $sJis = file_get_contents($filePath);
        $utf8 = mb_convert_encoding($sJis, 'UTF-8', 'Shift_jis');
        file_put_contents($filePath, $utf8);
    }

    /**
     * CSVファイルの
     * @return void
     */
    private function clean(string $filePath)
    {
        // file -i ファイル名のコマンドで文字コードを調べる
        $command = "file -i " . $filePath;
        $output = [];
        $status = "";
        exec($command, $output, $status);
        // エンコードを抜き取る
        preg_match("/charset=(.*)/", $output[0], $charset);
        // utf-8ではない場合処理実行
        if (!in_array("utf-8", $charset)) {
            #文字コードの変更※読込む前に変換しておく
            $sJis = file_get_contents($filePath);
            $utf8 = mb_convert_encoding($sJis, 'UTF-8', 'Shift_jis');
            file_put_contents($filePath, $utf8);
        }
    }
}
