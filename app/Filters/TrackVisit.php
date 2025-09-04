<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class TrackVisit implements FilterInterface
{
    // berapa detik dianggap idle = sesi “berakhir”
    private int $idleTimeout = 1800; // 30 menit

    public function before(RequestInterface $request, $arguments = null)
    {
        // update last_activity tiap request authenticated
        if (session()->get('user_id')) {
            $now = time();
            $last = (int) session()->get('last_activity');

            // jika idle terlalu lama dan ada visit_row_id yang belum terkunci, kunci sekarang
            if ($last && ($now - $last) > $this->idleTimeout) {
                $visitId = session()->get('visit_row_id');
                if ($visitId) {
                    $kunjungan = new \App\Models\KunjunganModel();
                    $row = $kunjungan->find($visitId);
                    if ($row && empty($row['logout_time'])) {
                        $logout = date('Y-m-d H:i:s');
                        $dtIn  = new \DateTime($row['login_time']);
                        $dtOut = new \DateTime($logout);
                        $durasi = $dtIn->diff($dtOut)->format('%H:%I:%S');

                        $kunjungan->update($visitId, [
                            'logout_time'  => $logout,
                            'durasi_waktu' => $durasi,
                        ]);
                    }
                    // kita biarkan session tetap ada; hanya menutup visit lama
                    session()->remove('visit_row_id');
                }
            }

            // segarkan last_activity
            session()->set('last_activity', time());
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // tidak perlu apa-apa di after
    }
}
