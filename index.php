<?php 
//程序员客栈 微代码派生 2020-05-06
echo “hello， git”;
////////////////////////

defined('IN_IA') or exit('Access Denied');
class Q_aaron_rencaiModule extends WeModule
{
    public $tablename = 'q_aaron_rencai_reply';   // aaronchen added by 2020-02-11
    public function fieldsFormDisplay($rid = 0)
    {
        global $_W;
        if (!empty($rid)) {
            $reply = pdo_fetch("SELECT * FROM " . tablename($this->tablename) . " WHERE acid = :acid AND rid = :rid ORDER BY `id` DESC", array(':acid' => $_W['uniacid'], ':rid' => $rid));
        }
        $home_url = $_W['siteroot'] . 'app/' . $this->createMobileUrl('index', array(), true);
        $home_url = str_replace('/./', '/', $home_url);
        load()->func('tpl');
        include $this->template('form');
    }
    public function fieldsFormValidate($rid = 0)
    {
        return true;
    }
    private function get_company2_label($company_label = '')
    {
        global $_W, $_GPC;
        if ($company_label) {
            return pdo_update('q_aaron_rencai_profile', array('val' => $company_label), array('weid' => $this->weid, 'key' => 'company2_label'));
        }
        $profile_row = pdo_fetch("SELECT * FROM " . tablename('q_aaron_rencai_profile') . " WHERE `key`='company2_label' and `weid`='" . $this->weid . "'");
        if ($profile_row) {
            return $profile_row['val'] ? $profile_row['val'] : '企业';
        } else {
            pdo_insert('q_aaron_rencai_profile', array('weid' => $this->weid, 'key' => 'company2_label', 'val' => '企业'));
            return '企业';
        }
    }
    public function fieldsFormSubmit($rid)
    {
        global $_GPC, $_W;
        $id = intval($_GPC['reply_id']);
        $data = array('acid' => $_W['uniacid'], 'rid' => $rid, 'title' => $_GPC['title'], 'avatar' => $_GPC['avatar'], 'description' => $_GPC['description'], 'dateline' => time());
        if (empty($id)) {
            pdo_insert($this->tablename, $data);
        } else {
            pdo_update($this->tablename, $data, array('id' => $id));
        }
    }
    public function ruleDeleted($rid)
    {
    }
    private function set_up_insert_into_origin_data()
    {
        global $_W, $_GPC;
        $para_data = pdo_fetch("SELECT * FROM " . tablename('uni_account_modules') . " WHERE module = :module AND uniacid = :uniacid", array(':module' => 'q_aaron_rencai', ':uniacid' => $_W['uniacid']));
        if (!$para_data || $para_data['settings'] == '') {
            $default_para_setting = 'a:25:{s:15:"resume_validity";s:16:"1
           $data_insert = array('uniacid' => $_W['uniacid'], 'module' => 'q_aaron_rencai', 'enabled' => 1, 'settings' => $default_para_setting);
            if (!$para_data) {
                pdo_insert('uni_account_modules', $data_insert);
            } else {
                pdo_update('uni_account_modules', $data_insert, array('id' => $para_data['id']));
            }
        }
    }
    public function settingsDisplay($settings)
    {
        global $_W, $_GPC;
        $this->set_up_insert_into_origin_data();
        $id = $_W['uniacid'];
        if (checksubmit()) {
            if (!empty($_FILES['fhbpaycert']['tmp_name'])) {
                load()->func('file');
                require_once IA_ROOT . '/addons/q_aaron_rencai/lib/PHPZIP.php';
                $ext = pathinfo($_FILES['fhbpaycert']['name'], PATHINFO_EXTENSION);
                if (strtolower($ext) != "zip") {
                    message("[文件格式错误]请上传您的微信支付证书哦~", referer(), 'error');
                }
                $wxcertdir = IA_ROOT . "/web/{$id}";
                if (!is_dir($wxcertdir)) {
                    mkdir($wxcertdir);
                }
                if (is_dir($wxcertdir)) {
                    if (!is_writable($wxcertdir)) {
                        message("请保证目录：[" . $wxcertdir . "]可写");
                    }
                }
                $save_file = $wxcertdir . "/" . $id . "." . $ext;
                file_move($_FILES['fhbpaycert']['tmp_name'], $save_file);
                $archive = new PHPZIP();
                $archive->unzip($save_file, $wxcertdir);
                file_delete($save_file);
                unlink($save_file);
            }
            $dat = array('resume_validity' => $_GPC['resume_validity'], 'payroll' => $_GPC['payroll'], 'welfare' => $_GPC['welfare'], 'educational' => $_GPC['educational'], 'positiontype' => $_GPC['positiontype'], 'workexperience' => $_GPC['workexperience'], 'companytype' => $_GPC['companytype'], 'scale' => $_GPC['scale'], 'qrcode' => $_GPC['qrcode'], 'telephone' => $_GPC['telephone'], 'isopenaudit' => $_GPC['isopenaudit'], 'viewresumenums' => $_GPC['viewresumenums'], 'isopenindextop' => !empty($_GPC['isopenindextop']) ? intval($_GPC['isopenindextop']) : 0, 'isopenindexhot' => !empty($_GPC['isopenindexhot']) ? intval($_GPC['isopenindexhot']) : 0, 'indextopnums' => !empty($_GPC['indextopnums']) ? intval($_GPC['indextopnums']) : 5, 'indexhotnums' => !empty($_GPC['indexhotnums']) ? intval($_GPC['indexhotnums']) : 5, 'indexlastnums' => intval($_GPC['indexlastnums']), 'indexcompanynums' => !empty($_GPC['indexcompanynums']) ? intval($_GPC['indexcompanynums']) : 5, 'show_companys_row' => !empty($_GPC['show_companys_row']) ? intval($_GPC['show_companys_row']) : 4, 'indexcompanynums_show_title' => $_GPC['indexcompanynums_show_title'], 'isopenlicense' => $_GPC['isopenlicense'], 'maxfilesize' => $_GPC['maxfilesize'], 'headimgurlsize' => $_GPC['headimgurlsize'], 'headimgurlwidth' => $_GPC['headimgurlwidth'], 'company_attach_pic_max' => intval($_GPC['company_attach_pic_max']), 'person_attach_pic_max' => intval($_GPC['person_attach_pic_max']), 'show_part_time' => $_GPC['show_part_time'], 'show_used_market' => $_GPC['show_used_market'], 'mobile_index_title' => $_GPC['mobile_index_title'], 'ad_speed' => $_GPC['ad_speed'], 'ad_time_of_lookresume' => $_GPC['ad_time_of_lookresume'], 'view_need_person_agree' => $_GPC['view_need_person_agree'], 'open_rencai_pay' => $_GPC['open_rencai_pay'], 'notify_auth_key' => $_GPC['notify_auth_key'], 'footer_nav_bgcolors' => $_GPC['footer_nav_bgcolors'] ? $_GPC['footer_nav_bgcolors'] : '#0e76ad', 'footer_nav_font_bgcolors' => $_GPC['footer_nav_font_bgcolors'], 'svs_appid' => $_GPC['svs_appid'], 'svs_appsecret' => $_GPC['svs_appsecret'], 'open_gps' => $_GPC['open_gps'], 'miniaddmoney' => $_GPC['miniaddmoney'], 'price_per_resume' => $_GPC['price_per_resume'], 'company_joinin_cost_per_year' => $_GPC['company_joinin_cost_per_year'], 'invites_per_member' => $_GPC['invites_per_member'], 'cfg_dft_p' => $_GPC['cfg_dft_p'], 'cfg_dft_c' => $_GPC['cfg_dft_c'], 'cfg_dft_d' => $_GPC['cfg_dft_d'], 'recommend_award_company' => $_GPC['recommend_award_company'], 'recommend_award_person' => $_GPC['recommend_award_person'], 'award_of_send_resume' => $_GPC['award_of_send_resume'], 'fhb_mchid' => $_GPC['fhb_mchid'], 'fhb_appid' => $_GPC['fhb_appid'], 'fhb_secret' => $_GPC['fhb_secret'], 'fhb_send_name' => $_GPC['fhb_send_name'], 'fhb_nick_name' => $_GPC['fhb_nick_name'], 'fhb_wishing' => $_GPC['fhb_wishing'], 'fhb_remark' => $_GPC['fhb_remark'], 'fhb_act_name' => $_GPC['fhb_act_name'], 'fhb_send_key' => $_GPC['fhb_send_key'], 'open_guanzhu' => $_GPC['open_guanzhu'], 'can_in_wap' => $_GPC['can_in_wap'], 'hide_noauth_joblist' => $_GPC['hide_noauth_joblist'], 'company_tel_need' => $_GPC['company_tel_need'], 'public_index_copyright' => $_GPC['public_index_copyright'], 'resume_top_pay_flag' => $_GPC['resume_top_pay_flag']);
            $this->saveSettings($dat);
            message('配置参数更新成功！', referer(), 'success');
        }
        if ($this->module['config']['footer_nav_bgcolors'] == '') {
            $this->module['config']['footer_nav_bgcolors'] = '#AD0E56';
        }
        $wxcertdir = IA_ROOT . "/web/{$id}/apiclient_cert.pem";
        $wxcertdir_flag = file_exists($wxcertdir);
        load()->func('tpl');
        include $this->template('setting');
    }
}

