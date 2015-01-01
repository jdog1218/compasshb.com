# config valid only for current version of Capistrano
lock '3.3.5'

set :application, "compasshb.com"
set :repo_url, "git@github.com:compasshb/compasshb.com.git"
set :tmp_dir, "~/tmp/"
set :deploy_to, "~/www/#{fetch(:application)}"
set :linked_files, %w{.env}

namespace :deploy do

  after :restart, :clear_cache do
    on roles(:web), in: :groups, limit: 3, wait: 10 do
      # Here we can do anything such as:
      # within release_path do
      #   execute :rake, 'cache:clear'
      # end
    end
  end

end
