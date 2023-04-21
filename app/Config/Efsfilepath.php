<?php

namespace Config;
use CodeIgniter\Config\BaseConfig;

class Efsfilepath extends BaseConfig {
    public function get_Efs_path(){
		$config['efs_path'] = '../../../../../EFS/webportalci4/';

        $config['efs_log'] = $config['efs_path'].'logs/';
        $config['efs_custom_log'] = $config['efs_log'].'custom_logs/';

        $config['efs_uploads'] = $config['efs_path'].'uploads/';
        $config['efs_uploads_bulk_dwn'] = $config['efs_uploads'].'bulk_download/';
        $config['efs_uploads_tds'] = $config['efs_uploads'].'tds/results/'; 

        $config['efs_linear_path'] = $config['efs_path'].'linear/'; 
        $config['efs_linear_preview_path'] =  $config['efs_linear_path'].'linear_preview/';  
        $config['efs_linear_sounds_path'] =  $config['efs_linear_path'].'sounds/';

        
        $config['efs_adaptive_path'] = $config['efs_uploads'].'adaptive/'; 
        $config['efs_anchor_path'] = $config['efs_uploads'].'anchor/'; 
        $config['efs_calibration_path'] = $config['efs_uploads'].'calibration/';

        $config['efs_brochure_path'] = $config['efs_uploads'].'documents/';

        $config['efs_banner_path'] = $config['efs_uploads'].'banner/';

        $config['efs_pages_path'] = $config['efs_uploads'].'pages/';

                
        $config['efs_charts'] = $config['efs_uploads'].'charts/';
        $config['efs_charts_results'] = $config['efs_charts'].'results/';

        $config['efs_uploads_sample_csv'] = $config['efs_uploads'].'sample_csv/';
        $config['efs_result_csv'] = $config['efs_uploads'].'result_calculation_csv/';

        return (object)$config;
	}
}