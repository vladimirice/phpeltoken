# Installation guide

1. [Basic preparations](#basic-preparations)
2. [Docker installation for Mac](#docker-installation-for-mac)
3. [Project installation](#project-installation)
4. [Docker installation for Windows](#docker-installation-for-windows)
5. [Docker installation for Linux](#docker-installation-for-linux)

## Basic preparations
Ensure that inside /etc/hosts file you have a basic localhost record:
   
    127.0.0.1 localhost

## Docker installation for Mac

This guide covers only installation of docker-machine with [xhyve driver](https://github.com/zchee/docker-machine-driver-xhyve).
You can also use [Docker Application for Mac](https://docs.docker.com/docker-for-mac/install/#download-docker-for-mac),
but performance of docker-machine + xhyve is better (but a bit less convenient to use).


### Let's install docker itself and required plugins

```shell
brew update
brew install docker docker-compose docker-machine
brew install --HEAD xhyve
brew install docker-machine-driver-xhyve
sudo chown root:wheel /usr/local/bin/docker-machine-driver-xhyve
sudo chmod u+s /usr/local/bin/docker-machine-driver-xhyve
```

Then let's create virtual machine itself with xhyve driver:
```shell
docker-machine create default --driver xhyve --xhyve-experimental-nfs-share --xhyve-memory-size=4000 --xhyve-disk-size=40000
```

Let's check than everything is ok - stop and start it.
```shell
docker-machine stop
docker-machine start
```
This is convenient way to catch any errors related to volume import problems, network problems, etc.

Then you have to set necessary environment variables. This is the most annoying part of docker-machine.
You have to do it every time for a new console session or write this down to your bash alias files.
```shell
eval $(docker-machine env default)
```

Let's check that there is no [volume empty error](https://github.com/zchee/docker-machine-driver-xhyve/issues/136)
```shell
docker-machine ssh
ls -l /Users/
```

First command opens ssh session (boot2docker + docker logo as an image). Second command must show the content of your
/Users folder. If folder is empty then there is a problem related to volumes (See the issue above). One of the rough solution
is to use [Docker Application for Mac](https://docs.docker.com/docker-for-mac/install/#download-docker-for-mac), not docker xhyve. 

## Project installation
* Run docker machine (if it is not run yet)
```shell
docker-machine start
eval $(docker-machine env default)
```

* Then there is a somehow "raw" moment of this documentation. You should know docker-machine IP in order to set hosts properly.
```shell
docker-machine ls
```

There is an IP address in output string. (ex. 192.168.64.3). Open a Makefile (root project dir), find a section 
docker_set_hosts_mac. Inside of this section change all IP address to the IP address above. Then run:
```shell
docker_set_hosts_mac
```

*Notice* - if you use [Docker Application for Mac](https://docs.docker.com/docker-for-mac/install/#download-docker-for-mac),
then do nothing above and just run:
```shell
docker_set_hosts_mac_localhost
```

At the end, let's create docker containers. You should answer Yes or press Enter if any questions are appeared.
```shell
make docker_compose_up
make docker_init_dev
```

If everything is ok you will see working project at:
[http://phpeltoken.dev/](http://phpeltoken.dev/)

## Docker installation for Windows

TODO 

I can only notice a couple of moments because my working computer is Mac.

At first you have to convert project file line endings (git). Without this docker bash files (linux-based) will not work
properly. Next, this link helps one of my friend a lot:
[Windows docker machine hyper-v](https://docs.docker.com/machine/drivers/hyper-v/#example)

## Docker installation for Linux

TODO