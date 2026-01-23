<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemilihan Ketua AMGPM Cabang Pniel - Voting Online</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEh-KAKSykFAZb2sojO0RD_6lJ__wqPE3aMhb5b-EUNS6TuD2g5-NXOwhYSr1TPc04ASGlMnoD6HBA25AQdiSXlLPbsv-Ymdw9M8IcoafsiGQJ9DyiQAmGe0X8lt2__xWk4_oZ-csyk0hkTP/s1600/LOGO+AMGPM+TERBARU+TRANS.png">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb',
                        'primary-dark': '#1d4ed8',
                        'primary-light': '#3b82f6',
                    },
                    fontFamily: {
                        'sans': ['Poppins', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
    </style>
    
    @livewireStyles
</head>
<body>
    
    <livewire:voting-livewire />

    @livewireScripts
    
    <!-- Auto-hide alerts after 5 seconds -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('voteSubmitted', () => {
                console.log('Vote submitted successfully!');
            });
        });

        setInterval(() => {
            const alerts = document.querySelectorAll('.animate-pulse');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            });
        }, 100);
    </script>
</body>
</html>