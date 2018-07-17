<?php
/**
 * 邮件服务
 * @filename  MailService.php
 * @author    Zhenxun Du <5552123@qq.com>
 * @date      2018/7/11 17:26
 */

namespace App\Services;

use Illuminate\Support\Facades\Mail;

class  MailService extends Service
{
    /**
     * 发邮件
     * @param string $subject 主题
     * @param string $str 内容
     * @param string $to 收件人 多人以 逗号 分隔
     * @param string $attach 附件 多个以 逗号 分隔
     * @param string $blade 模板
     */
    public function sendMail($subject, $str = '', $to = '', $attach = '', $blade = '')
    {
        if (!$to) {
            $to_arr = explode(',', config('mail.to_email'));
        } else {
            if (is_array($to)) {
                $to_arr = $to;
            } else {
                $to_arr = explode(',', $to);
            }
        }

        if ($attach) {
            if (is_array($attach)) {
                $attach_arr = $attach;
            } else {
                $attach_arr = explode(',', $attach);
            }
        }
        if (!$blade) {
            $blade = 'emails.mail';
        }
        Mail::send($blade, ['str' => $str], function ($message) use ($subject, $to_arr, $attach_arr) {

            $message->subject($subject . '[' . config('app.env') . ']');

            foreach ($to_arr as $mail) {
                $message->to($mail);
            }

            if (is_array($attach_arr) && count($attach_arr) > 0) {
                foreach ($attach_arr as $file) {
                    $message->attach($file);
                }
            }
        });
    }
}