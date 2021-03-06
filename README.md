# FUM-Election with Docker swarm
# 1.configure Docker swarm
first step is installing docker and virtualbox in ubuntu and use them in project.
after that we configure swarm cluster for election portals which consist of one manager node as LoadBalancer and two wrkers as election portals.
```
docker-machine create --driver virtualbox manager
docker-machine create --driver virtualbox worker1
docker-machine create --driver virtualbox worker2

```
after run the above commands, with docker-machine ls we can observe manager and workers IP's on the network.
by the below command we can go to manager node and recieve tokens of joined workers to the manager.
```
docker-machine ssh manager
swarm init --advertise-addr [manager node IP]

```
by the help of below commands we can go to both workers and attach them to manager by swarm join command.
```
docker-machine ssh worker1
docker swarm join --token SWMTKN-1-46kvmprfpbo03dofelt1foqnvks9keymweludtx8mej0ijhu77-1ahzn5au230kdmep8qmo79pwr 192.168.99.124:2377
docker-machine ssh worker2
docker swarm join --token SWMTKN-1-46kvmprfpbo03dofelt1foqnvks9keymweludtx8mej0ijhu77-1ahzn5au230kdmep8qmo79pwr 192.168.99.124:2377

```
add below command to create 2 replicas from php:7.2-apache service which is runing on the worker nodes.
attention! the difference between replicated and global is on --mode: 
in global mode, each service run in any active workers and its impossible to run more than one service on the worker(x) while in replica mode it may happen.
```
docker service create --name swarm-nodes --publish 8087:80 --mode global php:7.2-apache

```
for access to woker's bash or manager's bash, we must run following command:
```
docker exec -ti "container id of nodes" bash

```
index.php should be created on var/www/html directory. afterward php codes must develope on it.
since, the responsibility of manager node is load balancing, we must run following command on manager node,:
```
docker node update --availaibility drain manager

```
# 2.Election Portal code's in workers
After login, users will be redirected to the manager. manager conducts users to workers nodest.the worker nodes show election portal to users.
We have developed both master and fraud detection container based on slim framework. Slim is a php based framework. By the help of slim, we have developed our web services. 
After each step we have to send student number and token to the master node in order to send those values to the authentication container. If the authentication container return true, users are allowed to participate in election system. Accordingly, we have three request method:
```
http://masterIp:masterPort/slim/public/electionList?studentId={studnetId}&token={token}
http://masterIp:masterPort/slim/public/subElectionList?studentId={studnetId}&token={token}&id={electionId}
http://masterIp:masterPort/slim/public/subElectionList?studentId={studnetId}&token={token}&id={subElectionId}
```
as I mentioned before, master node developed by slim frame work. In master node we have four routes based on get method.
The master node must connect to three containers. First one is authentication, second one is fraud detection and the third one is database. Each user is able to participate in election if the fraud detection return true to the master. Each user is able to submit his/her vote just one time.
Four methods have been developed on master node:
```
/slim/public/electionList?studentId={studnetId}&token={token}
/slim/public/subElectionList?studentId={studnetId}&token={token}&id={electionId}
/slim/public/votet?studentId={studnetId}&token={token}&id={subElectionId}
/slim/public/permissible?studentId={studentId}&electionId={electionId}
```
