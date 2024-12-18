<?php 
    // チェックボックス形式でタグを表示
$tags_hobby = ["アウトドア",
             "インドア",
             "旅行",
             "読書",
             "音楽",
             "料理",
             "ゲーム",
             "釣り",
             "旅行",
             "麻雀",
             "パチンコ・スロット"
             ,"スポーツ"
             ,"漫画"
             ,"アイドル好き"
             ,"BL好き"
             ,"絵を描く"
             ,"外食",];

             
$tags_default_license = [
             "情報セキュリティマネジメント試験",
             "ネットワークスペシャリスト試験",
             "情報処理安全確保支援士試験",
             "データベーススペシャリスト試験",
             "システムアーキテクト試験",
             "プロジェクトマネージャ試験",
             "マイクロソフト認定技術者",
             "シスコ認定ネットワーク技術者",
             "オラクル認定ネットワーク技術者",
             "ビジネス能力ジョブパス２級",
             "ビジネス能力ジョブパス３級",
             "日商簿記２級",
             "日商簿記３級",
             "会計ソフト実務能力試験",
             "秘書技能検定試験",
             "マルチメディア検定",
             "Webデザイナー検定",
             "画像処理エンジニア検定",             
            ];

    $tags_ALL_common = [                
            "マイクロソフトオフィススペシャリストマスター",
            "マイクロソフトオフィススペシャリスト",
            "word expert",
            "word Specialist",
            "Excel expert",
            "Excel Specialist",
            "Access expert",
            "Access Specialist",
            "PowerPoint expert",
            "PowerPoint Specialist",
];

    $tags_IT_common = [
            "ITパスポート",
            "基本情報技術者試験",
            "応用情報技術者試験"
    ];

    $tags_design_common =[
        "CGクリエイター試験",
        "色彩検定１級",
        "色彩検定２級",
        "色彩検定３級",
        "Webクリエイター検定"
    ];

    $tags = array_merge($tags_hobby,$tags_default_license,$tags_ALL_common,$tags_IT_common,$tags_design_common)              
?>
