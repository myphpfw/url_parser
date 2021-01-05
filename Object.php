<?php
    declare(strict_types=1);
    require_once(__DIR__."/../database/Object.php");
    class url_parser {
        private string $request_uri;
        private array $request;
        private string $template;

        public function __construct() {
            $this->request_uri = $_SERVER["REQUEST_URI"];
            if(strlen($this->request_uri) > 1 && strpos($this->request_uri, "//") !== FALSE) {
                $this->request = explode("/", $this->request_uri);
            }
        }

        public function parse($db_config):void {
            $db = new database(host: $db_config["host"], user: $db_config["user"], pass: $db_config["pass"], name: $db_config["name"]);
            // Route Table Name
            $rtn = $db_config["prefix"]."routes";
            $db->prepare("SELECT * FROM ".$rtn." WHERE ".$rtn.".uri = :request_uri");
            $db->bind_and_execute([
                ":request_uri" => $this->request_uri,
            ]);
            $res = $db->fetch_assoc();
            // Check if only one path exists
            if(sizeof($res) !== 1 || isset($res[0]["path"]) === FALSE) {
                die("Unable to reliably determine include path for the requested URI.");
            }
            $this->template = $res[0]["path"];
        }

        public function execute_template():string {
            $content = "";
            // Check that both composer_modules loader module has been loaded, and Twig classes exist
            if(class_exists("composer_modules") === TRUE && class_exists("\Twig\Loader\FilesystemLoader") === TRUE) {
                $loader = new \Twig\Loader\FilesystemLoader(__DIR__."/../../templates");
                $twig = new \Twig\Environment($loader);
                $content = $twig->render($this->template);
            } else {
                $template_file = new get_include_contents(__DIR__."/../../templates".$this->template);
                $template_file->parse();
                $content = $template_file->return();
            }
            return $content;
        }
    }
