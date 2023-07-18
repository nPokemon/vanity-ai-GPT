import sys
import tiktoken


def count_tokens(encoding, content):
    encoding = tiktoken.get_encoding(encoding)
    tokens = encoding.encode(content)
    return len(tokens)

print(count_tokens(sys.argv[1], sys.argv[2]))
