# config valid for current version and patch releases of Capistrano
lock "~> 3.10.0"

set :application, "marathon"
set :repo_url, "ssh://git@128.199.130.27:/home/git/git-repo/marathon.git"

# Default branch is :master
# ask :branch, `git rev-parse --abbrev-ref HEAD`.chomp

# To make safe to deplyo to same server
#set :tmp_dir, "/home/marathon/tmp"

# Default deploy_to directory is /var/www/my_app_name
#set :deploy_to, '/home/marathon/website'
# set :deploy_to, "/var/www/my_app_name"

# Default value for :scm is :git
set :scm, :git

# Default value for :format is :airbrussh.
# set :format, :airbrussh

# You can configure the Airbrussh format using :format_options.
# These are the defaults.
# set :format_options, command_output: true, log_file: "log/capistrano.log", color: :auto, truncate: :auto

# Default value for :pty is false
set :pty, true

# Default value for :linked_files is []
append :linked_files, 'app/config/parameters.yml'
# append :linked_files, "config/database.yml", "config/secrets.yml"

# Default value for linked_dirs is []
append :linked_dirs, 'web/uploads', 'web/media', 'data/banks', 'data/excel', 'var/browscapcache', 'var/sessions'
# append :linked_dirs, "log", "tmp/pids", "tmp/cache", "tmp/sockets", "public/system"

# Default value for default_env is {}
# set :default_env, { path: "/opt/ruby/bin:$PATH" }

# Default value for local_user is ENV['USER']
# set :local_user, -> { `git config user.name`.chomp }

# Default value for keep_releases is 5
set :keep_releases, 3

# Uncomment the following to require manually verifying the host key before first deploy.
# set :ssh_options, verify_host_key: :secure

set :permission_method, :chmod
#set :file_permissions_paths, ["var", "var/browscapcache", "web/uploads", "web/media"]
#set :file_permissions_users, ["nginx"]

#after 'deploy:starting', 'composer:install_executable'
#after 'deploy:updated', 'deploy:installivoryckeditor'
after 'deploy:updated', 'symfony:assets:install'
after 'deploy:updated', 'deploy:migrate'

#namespace :deploy do
#  task :installivoryckeditor do
#    on roles(:app) do
#      symfony_console('ckeditor:install')
#    end
#  end
#end

namespace :deploy do
  task :migrate do
    on roles(:db) do
      symfony_console('doctrine:migrations:migrate', '--no-interaction')
    end
  end
end
