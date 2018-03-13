Amazon ECS container for barcode recognition
=======================

**Build container:** `docker build -t [container-name] [path_to_sources]` \
_Example_: `docker build -t barcode-recognition .`

**Run container:** `docker run [container-id] [arguments]` \
_Example_: 
```
docker run 8689b22ebbbd -e IMAGE_URL='https://s3-external-1.amazonaws.com/image/url/goes/here.jpg' \ 
    FUNCTION_NAME='lambda_function_name' \
    FUNCTION_QUALIFIER='production' \
    FUNCTION_TRACE_ID='someId' \
    AWS_REGION='us-west-2' \
    AWS_KEY='some-key' \
    AWS_SECRET='some-secret'
```

Where:
- IMAGE_URL - image URL to find barcode in
- FUNCTION_NAME - lambda function to pass recognized barcode
- FUNCTION_QUALIFIER - lambda function [qualifier](https://docs.aws.amazon.com/lambda/latest/dg/API_Invoke.html)
- FUNCTION_TRACE_ID - [AWS X-Ray](https://aws.amazon.com/xray/) trace id (optional)
- AWS_REGION - lambda function AWS region
- AWS_KEY - lambda function AWS key
- AWS_SECRET - lambda function AWS secret

Inspired by [How to use AWS Fargate and Lambda for long-running processes in a Serverless app](https://serverless.com/blog/serverless-application-for-long-running-process-fargate-lambda/)
