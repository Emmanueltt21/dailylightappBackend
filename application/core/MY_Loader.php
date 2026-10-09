<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * custom loader file extends CI_Loader
 */

class MY_Loader extends CI_Loader {
    public function template($template_name, $vars = array(), $return = FALSE)
    {
      $S3Header = $this->view('templates/header', $vars, $return); // header
      $S3Content = $this->view($template_name, $vars, $return); // view
      $S3Footer = $this->view('templates/footer', $vars, $return); // footer

      // Explicitly clear flashdata once viewed so they never linger across menu clicks
      if (isset($_SESSION['error'])) {
          unset($_SESSION['error']);
      }
      if (isset($_SESSION['success'])) {
          unset($_SESSION['success']);
      }
      if (isset($_SESSION['__ci_vars']['error'])) {
          unset($_SESSION['__ci_vars']['error']);
      }
      if (isset($_SESSION['__ci_vars']['success'])) {
          unset($_SESSION['__ci_vars']['success']);
      }

      if ($return)
      {
      /// return $content;

      return $S3Header; // default header
      return $S3Content; // view as controller
      return $S3Footer; // default footer

      }
    }

    public function views($template_name, $vars = array(), $return = FALSE)
    {
      //$S3Header = $this->view('templates/header', $vars, $return); // header
      $S3Content = $this->view($template_name, $vars, $return); // view
      //$S3Footer = $this->view('templates/footer', $vars, $return); // footer

      if ($return)
      {
      /// return $content;

      //return $S3Header; // default header
      return $S3Content; // view as controller
      //return $S3Footer; // default footer

      }
    }
}
