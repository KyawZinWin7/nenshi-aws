<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Super Admin user

         Employee::firstOrCreate(
            ['employee_code' => '56789'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('56789'),
                'department_id' => 1,
                'role' => 'superadmin',
            ]
        );

        // 撚糸課
        $employees = [
            ['name' => '山田　せつ子', 'employee_code' => '33327', 'department_id' => 1],
            ['name' => '酒井　由利子', 'employee_code' => '33331', 'department_id' => 1],
            ['name' => '坂本　裕子', 'employee_code' => '33765', 'department_id' => 1],
            ['name' => '中村　まつ子', 'employee_code' => '33892', 'department_id' => 1],
            ['name' => '原　重明', 'employee_code' => '34303', 'department_id' => 1],
            ['name' => '大平　利治', 'employee_code' => '34306', 'department_id' => 1],
            ['name' => '山田　幸朋', 'employee_code' => '34405', 'department_id' => 1],
            ['name' => '田鳥　富之', 'employee_code' => '34421', 'department_id' => 1],
            ['name' => '少覺　由美', 'employee_code' => '34444', 'department_id' => 1],
            ['name' => '中川　菜奈', 'employee_code' => '34451', 'department_id' => 1],
            ['name' => '酒井　絹子', 'employee_code' => '34508', 'department_id' => 1],
            ['name' => '葭田野　嘉弘', 'employee_code' => '34509', 'department_id' => 1],
            ['name' => '石井　隆重', 'employee_code' => '34510', 'department_id' => 1],
            ['name' => '石井　紀子', 'employee_code' => '34512', 'department_id' => 1],
            ['name' => '田中　聡子', 'employee_code' => '34515', 'department_id' => 1],
            ['name' => '松下　未来', 'employee_code' => '34520', 'department_id' => 1],
            ['name' => '鰀目　仁太', 'employee_code' => '34527', 'department_id' => 1],
            ['name' => '中川　寧々', 'employee_code' => '34536', 'department_id' => 1],
            ['name' => '橋本　唯我', 'employee_code' => '34537', 'department_id' => 1],
            ['name' => '吉田　絹子', 'employee_code' => '40074', 'department_id' => 1],
            ['name' => '宮下　恵', 'employee_code' => '40080', 'department_id' => 1],
            ['name' => '堀　咲季', 'employee_code' => '40083', 'department_id' => 1],
            ['name' => '木下　洋一', 'employee_code' => '40084', 'department_id' => 1],
            ['name' => 'ラ　ティ', 'employee_code' => '800161', 'department_id' => 1],
            ['name' => 'シュエ　リー　ウィン', 'employee_code' => '800162', 'department_id' => 1],
            ['name' => 'チュウ　チュウ　ルイン', 'employee_code' => '800163', 'department_id' => 1],
            ['name' => 'トン リン アウン', 'employee_code' => '800177', 'department_id' => 1],
            ['name' => 'ピョー ヘイン チョー', 'employee_code' => '800178', 'department_id' => 1],
            ['name' => 'エー ミン ソー', 'employee_code' => '800179', 'department_id' => 1],
            ['name' => 'ヤザー アウン', 'employee_code' => '800180', 'department_id' => 1],
            ['name' => 'メイ ミャッ ス', 'employee_code' => '800181', 'department_id' => 1],
            ['name' => 'テッ　シャイン　アウン', 'employee_code' => '800182', 'department_id' => 1],
            ['name' => 'トー　ハン', 'employee_code' => '800183', 'department_id' => 1],
            ['name' => 'ジン　ピョー　アウン', 'employee_code' => '800184', 'department_id' => 1],
            ['name' => 'ネー　トゥ　トゥエー', 'employee_code' => '800185', 'department_id' => 1],
            ['name' => 'カイン　ニラ　ウー', 'employee_code' => '800186', 'department_id' => 1],
            ['name' => 'ス　タイッ　ヤダナー', 'employee_code' => '800187', 'department_id' => 1],
        ];

        foreach ($employees as $emp) {
            Employee::firstOrCreate(
                ['employee_code' => $emp['employee_code']],
                [
                    'name' => $emp['name'],
                    'password' => Hash::make($emp['employee_code']),
                    'department_id' => $emp['department_id'],
                    'role' => 'user',
                ]
            );
        }

          // 準備課
        $department2Employees = [
            ['name' => '加藤　惠子', 'employee_code' => '33392'],
            ['name' => '南部　智樹', 'employee_code' => '34275'],
            ['name' => '棚田　真紀', 'employee_code' => '34299'],
            ['name' => '加藤　洋一', 'employee_code' => '34314'],
            ['name' => '石橋　歩実', 'employee_code' => '34395'],
            ['name' => '長谷川　弘美', 'employee_code' => '34403'],
            ['name' => '藤井　亮輔', 'employee_code' => '34410'],
            ['name' => '吉田　祥吾', 'employee_code' => '34422'],
            ['name' => '山田　佳奈', 'employee_code' => '34457'],
            ['name' => '中村　信穂', 'employee_code' => '34496'],
            ['name' => '水井　悠希', 'employee_code' => '34498'],
            ['name' => '山口　峻平', 'employee_code' => '34514'],
            ['name' => '袋田　恵', 'employee_code' => '34516'],
            ['name' => '近藤　涼', 'employee_code' => '34524'],
            ['name' => '小泉　直樹', 'employee_code' => '34535'],
            ['name' => '川端　裕美', 'employee_code' => '40050'],
            ['name' => '仲村 綾花', 'employee_code' => '40086'],
            ['name' => 'ティン　ザー　アウン', 'employee_code' => '800159'],
            ['name' => 'ジン　ジン　ライン', 'employee_code' => '800160'],
            ['name' => 'カウン ミャッ リン', 'employee_code' => '800173'],
            ['name' => 'アウン チョー タン', 'employee_code' => '800174'],
            ['name' => 'パン ワッ リー キン', 'employee_code' => '800175'],
            ['name' => 'ミ トゥ ザー', 'employee_code' => '800176'],
        ];

        foreach ($department2Employees as $emp) {
            Employee::firstOrCreate(
                ['employee_code' => $emp['employee_code']],
                [
                    'name' => $emp['name'],
                    'password' => Hash::make($emp['employee_code']),
                    'department_id' => 2,
                    'role' => 'user',
                ]
            );
        }
    }
}
