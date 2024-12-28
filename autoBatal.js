import { CronJob } from 'cron';
import { exec } from 'child_process';

// Membuat job cron untuk menjalankan perintah setiap menit
const job = new CronJob('* * * * *', () => {
    exec('php artisan schedule:run', (error, stdout, stderr) => {
        if (error) {
            console.error(`exec error: ${error}`);
            return;
        }
        console.log(`stdout: ${stdout}`);
        console.error(`stderr: ${stderr}`);
    });
});

job.start();
