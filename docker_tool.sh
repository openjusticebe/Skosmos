#!/usr/bin/env bash
APP_NAME=skosmos
REPO=openjustice
while getopts ":h" opt; do
  case ${opt} in
    h )
      echo "Usage:"
      echo "    docker-tool.sh -h        Display this help message."
      echo "    docker-tool.sh build <VERSION>    Builds image"
      echo "    docker-tool.sh test <VERSION>    Tests image"
      echo "    docker-tool.sh publish <VERSION>  Publishes image to Docker Hub"
      echo "    docker-tool.sh latest <VERSION>  Tags images as latest on Docker Hub."
      echo "    docker-tool.sh run <VERSION>  Run the image locally"
      exit 0
      ;;
   \? )
     echo "Invalid Option: -$OPTARG" 1>&2
     exit 1
     ;;
  esac
done


test_docker_image() {
     docker run -d --name "$1" -p 5005:80 -e HOST='0.0.0.0' $REPO/$APP_NAME:"$1"
     sleep 2
     url=http://localhost:5005/
     status=$(curl --get --location --connect-timeout 5 --write-out %{http_code} --silent --output /dev/null ${url})

     if [[ $status == '200' ]]
     then
      echo "$(tput setaf 2)Image: ${REPO}/${APP_NAME}:${1} - Passed$(tput sgr0)"
      docker kill "$1"
      docker rm "$1"
     else
      echo "$(tput setaf 1)Image: ${REPO}/${APP_NAME}:${1} - Failed$(tput sgr0)"
      docker kill "$1"
      docker rm "$1"
      exit 1
     fi
}

shift $((OPTIND -1))
subcommand=$1; shift
version=$1; shift

case "$subcommand" in
  build)
    docker build -t ${REPO}/${APP_NAME}:${version}  -f ./dockerfiles/Dockerfile.ubuntu --no-cache .
    ;;

  test)
    # Test the images
    test_docker_image ${version}
    ;;

  publish)
    # Push the build images
    docker push ${REPO}/${APP_NAME}:${version}
    ;;

  latest)
    # Update the latest tags to point to supplied version
    docker tag ${REPO}/${APP_NAME}:${version} ${REPO}/${APP_NAME}:latest
    docker push ${REPO}/${APP_NAME}:latest
    ;;

  run)
    # Run the image
    docker run -it --rm -p 6006:6006 ${REPO}/${APP_NAME}:${version}
    ;;

esac
