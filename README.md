Amazon ECS container for barcode recognition
=======================

**Build container:** `docker build -t [container-name] [path_to_sources]` \
_Example_: `docker build -t barcode-recognition .`

**Run container:** `docker run [container-id] [arguments]` \
_Example_: 
```
docker run \
 -e AWS_KEY='some-key' \
 -e AWS_REGION='us-west-2' \
 -e AWS_SECRET='some-secret' \
 -e FUNCTION_NAME='lambda_function_name' \
 -e FUNCTION_QUALIFIER='production' \
 -e FUNCTION_TRACE_ID='someId' \
 -e IMAGE_URL='https://s3-external-1.amazonaws.com/image/url/goes/here.jpg' \ 
 8689b22ebbbd
```

Where:
- AWS_KEY - lambda function AWS key
- AWS_REGION - lambda function AWS region
- AWS_SECRET - lambda function AWS secret
- FUNCTION_NAME - lambda function to pass recognized barcode
- FUNCTION_QUALIFIER - lambda function [qualifier](https://docs.aws.amazon.com/lambda/latest/dg/API_Invoke.html)
- FUNCTION_TRACE_ID - [AWS X-Ray](https://aws.amazon.com/xray/) trace id (optional)
- IMAGE_URL - image URL to find barcode in

Inspired by [How to use AWS Fargate and Lambda for long-running processes in a Serverless app](https://serverless.com/blog/serverless-application-for-long-running-process-fargate-lambda/)
