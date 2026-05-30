<?php

class Controller
{
    protected function view($view, $data = [])
    {
        extract($data);

        $isPartial = isset($_GET['partial']) && $_GET['partial'] == '1';
        $viewPath  = __DIR__ . '/../Views/' . $view . '.php';

        if (!file_exists($viewPath)) {
            http_response_code(404);
            echo "View not found: " . htmlspecialchars($view);
            exit;
        }

        if ($isPartial) {
            ob_start();
            require $viewPath;
            $pageTitle = htmlspecialchars($title ?? 'SIM Rumah Sakit', ENT_QUOTES, 'UTF-8');
            echo '<template data-partial-title="' . $pageTitle . '"></template>';
            echo protectPostForms(ob_get_clean());
            return;
        }

        ob_start();
        require __DIR__ . '/../Views/layouts/header.php';

        if (empty($auth_layout)) {
            require __DIR__ . '/../Views/layouts/sidebar.php';
            require __DIR__ . '/../Views/layouts/topbar.php';
        }

        echo '<main id="app-content">';
        echo '<div id="page-loader"><div class="loader-spinner"></div></div>';

        require $viewPath;

        echo '</main>';

        require __DIR__ . '/../Views/layouts/footer.php';
        echo protectPostForms(ob_get_clean());
    }


    public function frontView($view, $data = [])
    {
        extract($data);

        $viewFile = __DIR__ . '/../Views/' . $view . '.php';

        if (file_exists($viewFile)) {
            ob_start();
            require_once $viewFile;
            echo protectPostForms(ob_get_clean());
        } else {
            echo "View not found: " . $view;
        }
    }

    protected function redirect($page, $params = [])
    {
        header("Location: " . url($page, $params));
        exit;
    }

    protected function back()
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? url('dashboard');
        header("Location: " . $referer);
        exit;
    }
}
